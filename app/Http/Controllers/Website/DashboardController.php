<?php

namespace App\Http\Controllers\Website;

use App\Models\Feedback;
use App\Models\Poll;
use App\Models\UserQuizAnswer;
use App\Models\HomeSetting;
use App\Models\UserAttendence;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

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

        return view('website.dashboard', compact('home_setting', 'polls', 'activeCertificate'));

    }

    public function updateSessionTime(Request $request)
    {
        try {
            $userId = Auth::id();
            $now = Carbon::now();

            // Check if attendance tracking is enabled
            $home_setting = HomeSetting::first();
            if (!$home_setting || !$home_setting->user_attendance) {
                return response()->json([
                    'success' => false,
                    'message' => 'Attendance tracking is not enabled'
                ], 400);
            }

            if (!$this->checkAttendanceConditions($home_setting)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Attendance conditions not met'
                ], 400);
            }

            $attendance = UserAttendence::where('user_id', $userId)
                ->whereDate('joined_at', $now->toDateString())
                ->first();

            if (!$attendance) {
                $attendance = UserAttendence::create([
                    'user_id' => $userId,
                    'joined_at' => $now,
                    'last_ping_at' => $now,
                    'session_time' => 0
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Attendance created',
                    'session_time' => 0
                ]);
            }

            $lastPing = Carbon::parse($attendance->last_ping_at);
            $timeDiff = $lastPing->diffInSeconds($now);


            if ($timeDiff < 120) {
                $attendance->increment('session_time', $timeDiff);
            }

            $attendance->update([
                'last_ping_at' => $now
            ]);

            return response()->json([
                'success' => true,
                'session_time' => $attendance->session_time,
                'time_diff' => $timeDiff
            ]);

        } catch (\Exception $e) {
            Log::error('Attendance Update Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    private function checkAttendanceConditions($home_setting)
    {
        $now = Carbon::now();

        $activeFromDate = $home_setting->active_from_date ? Carbon::parse($home_setting->active_from_date) : null;
        $activeToDate = $home_setting->active_to_date ? Carbon::parse($home_setting->active_to_date) : null;
        $eventStartTime = $home_setting->event_start_time ? Carbon::parse($home_setting->event_start_time) : null;
        $eventEndTime = $home_setting->event_end_time ? Carbon::parse($home_setting->event_end_time) : null;

        if ($activeFromDate && $activeToDate) {
            if ($now->lt($activeFromDate) || $now->gt($activeToDate)) {
                Log::info('Attendance: Not in active period', [
                    'now' => $now->toDateTimeString(),
                    'from' => $activeFromDate->toDateTimeString(),
                    'to' => $activeToDate->toDateTimeString()
                ]);
                return false;
            }
        }

        if ($eventStartTime && $eventEndTime) {
            if ($now->lt($eventStartTime) || $now->gt($eventEndTime)) {
                Log::info('Attendance: Not in event time', [
                    'now' => $now->toDateTimeString(),
                    'start' => $eventStartTime->toDateTimeString(),
                    'end' => $eventEndTime->toDateTimeString()
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

        $attendance = UserAttendence::where('user_id', $userId)
            ->whereDate('joined_at', $now->toDateString())
            ->first();

        if (!$attendance) {
            UserAttendence::create([
                'user_id' => $userId,
                'joined_at' => $now,
                'last_ping_at' => $now,
                'session_time' => 0
            ]);
            Log::info('Attendance: New record created for user ' . $userId);
        } else {
            $attendance->update([
                'last_ping_at' => $now
            ]);
            Log::info('Attendance: Updated for user ' . $userId);
        }
    }
    public function store(Request $request)
    {
        $request->validate([
            'rating'  => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string',
        ]);

        $userId = auth()->id();

        $feedback = Feedback::firstOrNew(['user_id' => $userId]);

        $feedback->rating = $request->rating;
        $feedback->comment = $request->filled('comment') ? $request->comment : null;

        $feedback->save();

        return response()->json([
            'status' => true,
            'message' => 'Feedback saved successfully'
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
        'poll'  => $poll,
        'voted' => $vote
    ]);
}

public function submitPoll(Request $request)
{
    $request->validate([
        'poll_id' => 'required|exists:polls,id',
        'answer'  => 'required|string',
    ]);

    $alreadyVoted = UserQuizAnswer::where('poll_id', $request->poll_id)
        ->where('user_id', Auth::id())
        ->exists();

    if ($alreadyVoted) {
        return response()->json([
            'status' => false,
            'message' => 'You have already voted'
        ], 409);
    }

    UserQuizAnswer::create([
        'poll_id' => $request->poll_id,
        'user_id' => Auth::id(),
        'answer'  => $request->answer,
    ]);

    return response()->json([
        'status' => true,
        'message' => 'Vote submitted successfully'
    ]);
}




}
