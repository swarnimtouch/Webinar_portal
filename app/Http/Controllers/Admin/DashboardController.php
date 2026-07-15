<?php

namespace App\Http\Controllers\Admin;

use App\Models\Events;
use App\Models\Feedback;
use App\Models\Poll;
use App\Models\Thread;
use App\Models\User;
use App\Models\UserAttendance;
use App\Models\UserPollAnswer;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $authUser = auth()->user();
        $isSubAdmin = $authUser->type === 'sub_admin';
        $eventId = $isSubAdmin ? $authUser->event_id : null;

        $scope = static fn ($query, string $column = 'event_id') => $isSubAdmin
            ? $query->where($column, $eventId ?? 0)
            : $query;

        $users = $scope(User::query()->where('type', 'doctor'));
        $polls = $scope(Poll::query());
        $votes = UserPollAnswer::query()->whereHas('poll', fn ($query) => $scope($query));
        $attendance = UserAttendance::query()->whereHas('user', function ($query) use ($scope) {
            $scope($query->where('type', 'doctor'));
        });

        $liveCutoff = now()->subMinutes(2);
        $stats = [
            'events' => $isSubAdmin ? ($eventId ? 1 : 0) : Events::count(),
            'registrations' => (clone $users)->count(),
            'new_today' => (clone $users)->whereDate('created_at', today())->count(),
            'live_users' => (clone $attendance)->where('last_ping_at', '>=', $liveCutoff)->distinct('user_id')->count('user_id'),
            'attendees' => (clone $attendance)->distinct('user_id')->count('user_id'),
            'session_seconds' => (int) (clone $attendance)->sum('session_time'),
            'polls' => (clone $polls)->count(),
            'votes' => (clone $votes)->count(),
            'voters' => (clone $votes)->distinct('user_id')->count('user_id'),
            'feedback' => $scope(Feedback::query())->count(),
            'chat_threads' => $scope(Thread::query())->count(),
        ];

        $chart = collect(range(6, 0))->map(function ($daysAgo) use ($scope) {
            $day = Carbon::today()->subDays($daysAgo);
            $registered = $scope(User::query()->where('type', 'doctor'))
                ->whereDate('created_at', $day)->count();
            $attended = UserAttendance::query()
                ->whereDate('joined_at', $day)
                ->whereHas('user', function ($query) use ($scope) {
                    $scope($query->where('type', 'doctor'));
                })->distinct('user_id')->count('user_id');

            return ['label' => $day->format('D'), 'date' => $day->format('d M'), 'registered' => $registered, 'attended' => $attended];
        });

        $recentUsers = (clone $users)->with('event:id,name')->latest()->limit(8)->get();
        $eventRows = $scope(Events::query(), 'id')->latest()->get()->map(function ($event) use ($liveCutoff) {
            $eventUsers = User::where('type', 'doctor')->where('event_id', $event->id);
            $eventAttendance = UserAttendance::whereHas('user', fn ($query) => $query->where('event_id', $event->id));
            $eventVotes = UserPollAnswer::whereHas('poll', fn ($query) => $query->where('event_id', $event->id));

            return [
                'event' => $event,
                'registrations' => $eventUsers->count(),
                'live' => (clone $eventAttendance)->where('last_ping_at', '>=', $liveCutoff)->distinct('user_id')->count('user_id'),
                'attendees' => (clone $eventAttendance)->distinct('user_id')->count('user_id'),
                'polls' => Poll::where('event_id', $event->id)->count(),
                'votes' => $eventVotes->count(),
            ];
        });

        return view('admin.dashboard', [
            'title' => __('Dashboard'),
            'breadcrumb' => breadcrumb([__('Dashboard') => route('admin.dashboard')]),
            'isSubAdmin' => $isSubAdmin,
            'assignedEvent' => $isSubAdmin ? Events::find($eventId) : null,
            'stats' => $stats,
            'chart' => $chart,
            'recentUsers' => $recentUsers,
            'eventRows' => $eventRows,
        ]);
    }
}
