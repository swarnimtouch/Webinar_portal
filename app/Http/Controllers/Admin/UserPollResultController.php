<?php

namespace App\Http\Controllers\Admin;

use App\Models\Events;
use Illuminate\Http\Request;
use App\Models\UserPollAnswer;

class UserPollResultController
{
    public function index()
    {
        $user = auth()->user();

        $eventIds = UserPollAnswer::with('poll')
            ->get()
            ->pluck('poll.event_id')
            ->filter()
            ->unique()
            ->values();

        $events = Events::whereIn('id', $eventIds)
            ->orderBy('name')
            ->get();

        if ($user->type === 'sub_admin') {
            $events = $events->where('id', $user->event_id)->values();
        }

        return view('admin.user_poll_result.index', [
            'title' => __('User Poll Result'),
            'events' => $events,
            'breadcrumb' => breadcrumb([
                __('User Poll Result') => route('admin.user_poll_result')
            ])
        ]);
    }

    public function delete($id)
    {
        try {
            $userPollAnswer = UserPollAnswer::findOrFail($id);
            $userPollAnswer->delete();

            return response()->json(['success' => true, 'message' => 'User Poll Answer deleted successfully']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error deleting User Poll Answer'], 500);
        }
    }

    public function deleteMultiple(Request $request)
    {
        try {
            $ids = $request->input('ids', []);

            if (empty($ids)) {
                return response()->json(['success' => false, 'message' => 'No User Poll Answer selected'], 400);
            }

            UserPollAnswer::whereIn('id', $ids)->delete();

            return response()->json(['success' => true, 'message' => 'User Poll Answer deleted successfully']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error deleting User Poll Answer'], 500);
        }
    }

    public function datatable(Request $request)
    {
        $user = auth()->user();

        $query = UserPollAnswer::with(['user', 'poll.event', 'pollAnswer']);

        if ($user->type === 'sub_admin') {
            $query->whereHas('poll', function ($q) use ($user) {
                $q->where('event_id', $user->event_id);
            });
        }
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('user', function ($userQuery) use ($search) {
                    $userQuery->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                })
                    ->orWhereHas('poll', function ($pollQuery) use ($search) {
                        $pollQuery->where('question', 'like', "%{$search}%");
                    })
                    ->orWhere('answer', 'like', "%{$search}%");
            });
        }
        if ($request->filled('event')) {
            $query->whereHas('poll', function ($q) use ($request) {
                $q->where('event_id', $request->event);
            });
        }

        $total = $query->count();

        if ($request->has('order')) {
            $columns = $request->columns;
            foreach ($request->order as $order) {
                $columnIndex = $order['column'];
                $columnName = $columns[$columnIndex]['data'];
                $direction = $order['dir'];

                $dbColumn = match ($columnName) {
                    'answer' => 'answer',
                    'created_at' => 'created_at',
                    default => 'id',
                };

                $query->orderBy($dbColumn, $direction);
            }
        } else {
            $query->orderBy('id', 'desc');
        }

        $length = $request->input('length', 10);
        $start = $request->input('start', 0);

        $userPollAnswers = $query->skip($start)->take($length)->get();

        $data = $userPollAnswers->map(function ($userPollAnswer) {
            return [
                'id' => $userPollAnswer->id,
                'event' => $userPollAnswer->poll?->event?->name ?? 'N/A',
                'user_name' => optional($userPollAnswer->user)->name ?? 'N/A',
                'user_email' => optional($userPollAnswer->user)->email ?? 'N/A',
                'question' => optional($userPollAnswer->poll)->question ?? 'N/A',
                'answer' => $userPollAnswer->pollAnswer?->answer
                    ?? $userPollAnswer->answer
                        ?? 'N/A',
                'created_at' => $userPollAnswer->created_at?->format('d M Y') ?? 'N/A',
                'actions' => '',
            ];
        });

        return response()->json([
            'draw' => (int)$request->input('draw'),
            'recordsTotal' => $total,
            'recordsFiltered' => $total,
            'data' => $data,
        ]);
    }

    public function export(Request $request)
    {
        $user = auth()->user();
        $isAdmin = $user->type === 'admin';
        $search = $request->get('search');

        $query = UserPollAnswer::with(['poll.event', 'user', 'pollAnswer'])
            ->when(!$isAdmin, function ($q) use ($user) {
                $q->whereHas('poll', function ($pq) use ($user) {
                    $pq->where('event_id', $user->event_id);
                });
            })
            ->when($search, function ($q) use ($search) {
                $q->where(function ($q) use ($search) {
                    $q->whereHas('user', function ($uq) use ($search) {
                        $uq->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    })
                        ->orWhereHas('poll', function ($pq) use ($search) {
                            $pq->where('question', 'like', "%{$search}%");
                        })
                        ->orWhere('answer', 'like', "%{$search}%");
                });
            })
            ->orderBy('id', 'desc')
            ->get();

        $headers = $isAdmin
            ? ['Event', 'User', 'Email', 'Question', 'Answer', 'Date']
            : ['User', 'Email', 'Question', 'Answer', 'Date'];

        $rows = [];
        $rows[] = implode(',', $headers);

        $esc = fn($val) => '"' . str_replace('"', '""', $val) . '"';

        foreach ($query as $answer) {
            $eventName = $answer->poll?->event?->name ?? 'N/A';
            $userName = optional($answer->user)->name ?? 'N/A';
            $userEmail = optional($answer->user)->email ?? 'N/A';
            $question = optional($answer->poll)->question ?? 'N/A';
            $ans = $answer->pollAnswer?->answer ?? $answer->answer ?? 'N/A';
            $date = $answer->created_at?->format('d M Y') ?? 'N/A';

            $row = $isAdmin
                ? [$esc($eventName), $esc($userName), $esc($userEmail), $esc($question), $esc($ans), $esc($date)]
                : [$esc($userName), $esc($userEmail), $esc($question), $esc($ans), $esc($date)];

            $rows[] = implode(',', $row);
        }

        $csv = implode("\n", $rows);
        $filename = 'user_poll_results_export_' . now()->format('Y-m-d') . '.csv';

        return response($csv, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }
}
