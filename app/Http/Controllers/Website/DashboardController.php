<?php

namespace App\Http\Controllers\Website;

use App\Models\Certificate;
use App\Models\Feedback;
use App\Models\Poll;
use App\Models\UserAttendance;
use App\Models\UserPollAnswer;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Support\EventStorage;

class DashboardController
{
    private $event = null;

    public function __construct()
    {
        $this->event = app('event');
    }

    public function dashboard()
    {
        $polls = Poll::activeVisibleLatest()->CurrentEvent()->get();
        $activeCertificate = Certificate::CurrentEvent()->Active()->first();
        $feedback = Feedback::where('user_id', Auth::guard('web')->id())
            ->where('event_id', $this->event->id ?? null)
            ->first();
        $resources = $this->event->resources()->orderBy('slot')->get();

        return view('website.dashboard', [
            'polls' => $polls,
            'active_certificate' => $activeCertificate,
            'feedback' => $feedback,
            'resources' => $resources,
            'is_log_attendance' => $this->event->is_log_attendance ?? false,
            'title' => __('Dashboard'),
        ]);
    }

    public function downloadResource(Request $request)
    {
        $resourceId = $request->route('resourceId');
        $resource = $this->event->resources()->findOrFail($resourceId);

        abort_unless(EventStorage::exists($resource->file_path), 404);

        return response(EventStorage::contents($resource->file_path), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="' . addslashes($resource->original_name) . '"',
        ]);
    }

    public function attendanceJoin(Request $request)
    {
        try {

            if (!$this->event->is_log_attendance) {
                return response()->json(['success' => false], 400);
            }

            if (!$this->checkAttendanceConditions()) {
                return response()->json(['success' => false, 'message' => 'Outside event window'], 400);
            }

            $userId = Auth::guard('web')->id();
            $now = Carbon::now();

            $attendance = DB::transaction(function () use ($userId, $now) {
                // Lock the user row so simultaneous heartbeat requests cannot
                // add the same seconds twice or create duplicate attendance.
                \App\Models\User::whereKey($userId)->lockForUpdate()->firstOrFail();

                $attendance = UserAttendance::firstOrCreate(
                    ['user_id' => $userId],
                    ['joined_at' => $now, 'last_ping_at' => null, 'session_time' => 0]
                );

                if ($attendance->last_ping_at) {
                    $elapsed = Carbon::parse($attendance->last_ping_at)->diffInSeconds($now);

                    // Heartbeats run every 30 seconds. A larger gap means the
                    // previous browser session disappeared without a leave ping.
                    if ($elapsed > 0 && $elapsed <= 90) {
                        $attendance->session_time += $elapsed;
                    }
                }

                $attendance->last_ping_at = $now;
                $attendance->save();

                return $attendance;
            });

            return response()->json([
                'success' => true,
                'session_time' => $attendance->session_time,
            ]);

        } catch (\Exception $e) {
            Log::error('Attendance Join: ' . $e->getMessage());
            return response()->json(['success' => false], 500);
        }
    }

    public function attendanceLeave(Request $request)
    {
        try {
            $userId = Auth::guard('web')->id();
            $now = Carbon::now();

            DB::transaction(function () use ($userId, $now) {
                \App\Models\User::whereKey($userId)->lockForUpdate()->first();
                $attendance = UserAttendance::where('user_id', $userId)->first();

                if (!$attendance || !$attendance->last_ping_at) return;

                $elapsed = Carbon::parse($attendance->last_ping_at)->diffInSeconds($now);
                if ($elapsed > 0 && $elapsed <= 90) {
                    $attendance->session_time += $elapsed;
                }

                // Null marks the session closed, so time away is never counted
                // when this user comes back to the event later.
                $attendance->last_ping_at = null;
                $attendance->save();
            });

            return response()->json(['success' => true]);

        } catch (\Exception $e) {
            Log::error('Attendance Leave: ' . $e->getMessage());
            return response()->json(['success' => false], 500);
        }
    }

    public function feedbackSave(Request $request)
    {
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string',
        ]);

        $feedback = Feedback::firstOrNew([
            'user_id' => auth()->guard('web')->id(),
            'event_id' => $this->event->id ?? null,
        ]);
        $feedback->rating = $request->rating;
        $feedback->comment = $request->filled('comment') ? $request->comment : null;
        $feedback->save();

        return response()->json([
            'status' => true,
            'message' => 'Feedback saved successfully',
            'feedback' => [
                'rating' => $feedback->rating,
                'comment' => $feedback->comment,
            ],
        ]);
    }

    public function getPoll()
    {
        $poll = Poll::where('status', 'active')
            ->CurrentEvent()
            ->withCount('votes')
            ->with(['poll_answers' => function ($query) {
                $query->withCount('user_voted');
            }])
            ->where('is_hidden', 0)
            ->latest()
            ->first();
        if (!$poll) {
            return response()->json(['poll' => null]);
        }

        $vote = UserPollAnswer::where('poll_id', $poll->id)
            ->where('user_id', Auth::guard('web')->id())
            ->first();

        return response()->json(['poll' => $poll, 'voted' => $vote]);
    }

    public function submitPoll(Request $request)
    {
        $request->validate([
            'poll_id' => 'required|exists:polls,id',
            'answer_id' => 'required|exists:poll_answers,id',
            'answer' => 'required',
        ]);

        $alreadyVoted = UserPollAnswer::where('poll_id', $request->poll_id)
            ->where('user_id', Auth::id())
            ->exists();

        if ($alreadyVoted) {
            return response()->json(['status' => false, 'message' => 'You have already voted'], 409);
        }

        UserPollAnswer::create([
            'poll_id' => $request->poll_id,
            'user_id' => Auth::id(),
            'answer' => $request->answer,
            'answer_id' => $request->answer_id,
        ]);

        return response()->json(['status' => true, 'message' => 'Vote submitted successfully']);
    }

    private function checkAttendanceConditions(): bool
    {
        $now = Carbon::now('Asia/Kolkata');

        $from = $this->event->active_user_from ? Carbon::parse($this->event->active_user_from, 'Asia/Kolkata') : null;
        $to = $this->event->active_user_to ? Carbon::parse($this->event->active_user_to, 'Asia/Kolkata') : null;

        if ($from && $to && ($now->lt($from) || $now->gt($to))) return false;

        $start = $this->event->start_time ? Carbon::parse($this->event->start_time, 'Asia/Kolkata') : null;
        $end = $this->event->end_time ? Carbon::parse($this->event->end_time, 'Asia/Kolkata') : null;

        if ($start && $end && ($now->lt($start) || $now->gt($end))) return false;

        return true;
    }
}
