<?php

namespace App\Http\Controllers\Admin;

use App\Models\Events;
use App\Models\Comment;
use App\Models\Feedback;
use App\Models\Poll;
use App\Models\Thread;
use App\Models\User;
use App\Models\UserAttendance;
use App\Models\UserPollAnswer;
use Carbon\Carbon;
use Illuminate\Http\Request;

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
            'comments' => $scope(Comment::query())->count(),
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
        $assignedEvent = $isSubAdmin ? Events::find($eventId) : null;
        $locationReports = collect(['country', 'state', 'city'])
            ->filter(fn (string $dimension) => $isSubAdmin && $assignedEvent?->{"show_{$dimension}_report"})
            ->mapWithKeys(function (string $dimension) use ($users) {
                $rows = (clone $users)
                    ->selectRaw("COALESCE(NULLIF(TRIM({$dimension}), ''), 'Not specified') as location, COUNT(*) as total")
                    ->groupBy('location')
                    ->orderByDesc('total')
                    ->orderBy('location')
                    ->get();

                return [$dimension => $rows];
            });
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
            'assignedEvent' => $assignedEvent,
            'stats' => $stats,
            'chart' => $chart,
            'recentUsers' => $recentUsers,
            'eventRows' => $eventRows,
            'locationReports' => $locationReports,
        ]);
    }

    public function exportLocationReport(Request $request)
    {
        $authUser = auth()->user();
        abort_unless($authUser->type === 'sub_admin' && $authUser->event_id, 403);

        $dimension = $request->string('dimension')->lower()->value();
        abort_unless(in_array($dimension, ['country', 'state', 'city'], true), 404);

        $event = Events::findOrFail($authUser->event_id);
        abort_unless($event->{"show_{$dimension}_report"}, 403);

        $rows = User::query()
            ->where('type', 'doctor')
            ->where('event_id', $event->id)
            ->selectRaw("COALESCE(NULLIF(TRIM({$dimension}), ''), 'Not specified') as location, COUNT(*) as total")
            ->groupBy('location')
            ->orderByDesc('total')
            ->orderBy('location')
            ->get();

        $escape = fn ($value) => '"' . str_replace('"', '""', (string) $value) . '"';
        $csv = [ucfirst($dimension) . ',Registered Users'];
        foreach ($rows as $row) {
            $csv[] = $escape($row->location) . ',' . $row->total;
        }

        $filename = "{$dimension}_wise_registered_users_" . now()->format('Y-m-d') . '.csv';

        return response("\xEF\xBB\xBF" . implode("\n", $csv), 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }
}
