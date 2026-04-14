<?php

namespace App\Http\Controllers\Website;

use App\Models\Feedback;
use App\Models\Poll;
use App\Models\UserQuizAnswer;
use App\Models\HomeSetting;
use App\Models\UserAttendance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use App\Models\Certificate;

class DashboardController
{
    public function dashboard()
    {
        $home_setting = HomeSetting::first();

        if ($home_setting && $home_setting->user_attendance) {
            $this->trackUserAttendance($home_setting);
        }

        $polls = Poll::where('status', 'active')
            ->where('is_hidden', 0)
            ->orderBy('id', 'desc')
            ->get();

        $activeCertificate = Certificate::where('status', 'active')->first();

        return view('website.dashboard', [
            'home_setting' => $home_setting,
            'polls' => $polls,
            'activeCertificate' => $activeCertificate,
            'title' => __('Dashboard'),
        ]);
    }

    public function updateSessionTime(Request $request)
    {
        try {
            $userId = Auth::id();
            $now = Carbon::now();

            $home_setting = HomeSetting::first();

            if (!$home_setting || !$home_setting->user_attendance) {
                return response()->json([
                    'success' => false,
                    'message' => 'Attendance tracking is not enabled',
                ], 400);
            }

            if (!$this->checkAttendanceConditions($home_setting)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Attendance conditions not met',
                ], 400);
            }

            $attendance = UserAttendance::where('user_id', $userId)
                ->whereDate('joined_at', $now->toDateString())
                ->first();

            if (!$attendance) {
                $attendance = UserAttendance::create([
                    'user_id' => $userId,
                    'joined_at' => $now,
                    'last_ping_at' => $now,
                    'session_time' => 0,
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Attendance created',
                    'session_time' => 0,
                ]);
            }

            $lastPing = Carbon::parse($attendance->last_ping_at);
            $timeDiff = $lastPing->diffInSeconds($now);

            if ($timeDiff < 120) {
                $attendance->increment('session_time', $timeDiff);
            }

            $attendance->update([
                'last_ping_at' => $now,
            ]);

            return response()->json([
                'success' => true,
                'session_time' => $attendance->session_time,
                'time_diff' => $timeDiff,
            ]);

        } catch (\Exception $e) {
            Log::error('Attendance Update Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    private function checkAttendanceConditions($home_setting)
    {
        $now = Carbon::now('Asia/Kolkata');

        $activeFromDate = $home_setting->active_from_date
            ? Carbon::parse($home_setting->active_from_date, 'Asia/Kolkata')
            : null;

        $activeToDate = $home_setting->active_to_date
            ? Carbon::parse($home_setting->active_to_date, 'Asia/Kolkata')
            : null;

        if ($activeFromDate && $activeToDate) {
            if ($now->lt($activeFromDate) || $now->gt($activeToDate)) {
                Log::info('Attendance: Not in active period', [
                    'now' => $now->toDateTimeString(),
                    'from' => $activeFromDate->toDateTimeString(),
                    'to' => $activeToDate->toDateTimeString(),
                ]);
                return false;
            }
        }

        $eventStartTime = $home_setting->event_start_time
            ? Carbon::parse($home_setting->event_start_time, 'Asia/Kolkata')
            : null;

        $eventEndTime = $home_setting->event_end_time
            ? Carbon::parse($home_setting->event_end_time, 'Asia/Kolkata')
            : null;

        if ($eventStartTime && $eventEndTime) {
            $todayStart = Carbon::today('Asia/Kolkata')
                ->setHour($eventStartTime->hour)
                ->setMinute($eventStartTime->minute)
                ->setSecond(0);

            $todayEnd = Carbon::today('Asia/Kolkata')
                ->setHour($eventEndTime->hour)
                ->setMinute($eventEndTime->minute)
                ->setSecond(0);

            if ($now->lt($todayStart) || $now->gt($todayEnd)) {
                Log::info('Attendance: Not in event time', [
                    'now' => $now->toDateTimeString(),
                    'start' => $todayStart->toDateTimeString(),
                    'end' => $todayEnd->toDateTimeString(),
                ]);
                return false;
            }
        }

        return true;
    }

    private function trackUserAttendance($home_setting)
    {
        if (!$this->checkAttendanceConditions($home_setting)) {
            return;
        }

        $userId = Auth::id();
        $now = Carbon::now();

        $attendance = UserAttendance::where('user_id', $userId)
            ->whereDate('joined_at', $now->toDateString())
            ->first();

        if (!$attendance) {
            UserAttendance::create([
                'user_id' => $userId,
                'joined_at' => $now,
                'last_ping_at' => $now,
                'session_time' => 0,
            ]);
            Log::info('Attendance: New record created for user ' . $userId);
        } else {
            $attendance->update([
                'last_ping_at' => $now,
            ]);
            Log::info('Attendance: Updated for user ' . $userId);
        }
    }

    public function store(Request $request)
    {
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string',
        ]);

        $userId = auth()->id();

        $feedback = Feedback::firstOrNew(['user_id' => $userId]);

        $feedback->rating = $request->rating;
        $feedback->comment = $request->filled('comment') ? $request->comment : null;

        $feedback->save();

        return response()->json([
            'status' => true,
            'message' => 'Feedback saved successfully',
        ]);
    }

    public function getPoll()
    {
        $poll = Poll::where('status', 'active')
            ->where('is_hidden', 0)
            ->latest()
            ->first();

        if (!$poll) {
            return response()->json(['poll' => null]);
        }

        $vote = UserQuizAnswer::where('poll_id', $poll->id)
            ->where('user_id', Auth::id())
            ->first();

        return response()->json([
            'poll' => $poll,
            'voted' => $vote,
        ]);
    }

    public function submitPoll(Request $request)
    {
        $request->validate([
            'poll_id' => 'required|exists:polls,id',
            'answer' => 'required|string',
        ]);

        $alreadyVoted = UserQuizAnswer::where('poll_id', $request->poll_id)
            ->where('user_id', Auth::id())
            ->exists();

        if ($alreadyVoted) {
            return response()->json([
                'status' => false,
                'message' => 'You have already voted',
            ], 409);
        }

        UserQuizAnswer::create([
            'poll_id' => $request->poll_id,
            'user_id' => Auth::id(),
            'answer' => $request->answer,
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Vote submitted successfully',
        ]);
    }
}
