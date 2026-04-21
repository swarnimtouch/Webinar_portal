<?php

namespace App\Http\Controllers\Admin;

use App\Models\Messages;
use App\Models\User;
use Illuminate\Http\Request;

class ChatLogController extends Controller
{
    /**
     * Chat messages listing page
     */
    public function index()
    {

        return view('admin.chat_log.index', [
            'title' => __('Chat Log'),
            'breadcrumb' => breadcrumb([
                __('Chat Log') => route('admin.chat_log')
            ])
        ]);
    }

    /**
     * Delete single chat message
     */
    public function delete($id)
    {
        try {
            $chatMessage = Messages::findOrFail($id);
            $chatMessage->delete();

            return response()->json([
                'success' => true,
                'message' => 'Chat Log deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting chat Log'
            ], 500);
        }
    }

    /**
     * Delete multiple chat messages
     */
    public function deleteMultiple(Request $request)
    {
        try {
            $ids = $request->input('ids', []);

            if (empty($ids)) {
                return response()->json([
                    'success' => false,
                    'message' => 'No chat Log selected'
                ], 400);
            }

            Messages::whereIn('id', $ids)->delete();

            return response()->json([
                'success' => true,
                'message' => 'Chat Log deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting chat Log'
            ], 500);
        }
    }

    /**
     * Datatable for chat messages (Admin)
     */
    public function datatable(Request $request)
    {
        $user = auth()->user();

        $query = Messages::with(['sender', 'group.event']);
        if ($user->type === 'sub_admin') {
            $query->whereHas('sender', function ($q) use ($user) {
                $q->where('event_id', $user->event_id);
            });
        }
        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('message', 'like', "%{$search}%")
                    ->orWhereHas('sender', function ($userQuery) use ($search) {
                        $userQuery->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    })
                    ->orWhereHas('group', function ($groupQuery) use ($search) {
                        $groupQuery->where('name', 'like', "%{$search}%");
                    });
            });
        }

        if ($request->filled('group_id')) {
            $query->where('group_id', $request->group_id);
        }

        $total = $query->count();

        if ($request->has('order')) {
            $columns = $request->columns;

            foreach ($request->order as $order) {
                $columnIndex = $order['column'];
                $columnName = $columns[$columnIndex]['data'];
                $direction = $order['dir'];

                $dbColumn = match ($columnName) {
                    'message' => 'message',
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

        $chatMessages = $query->skip($start)->take($length)->get();

        $data = $chatMessages->map(function ($chatMessage) {
            $seenUserNames = 'Not seen';

            if (!empty($chatMessage->seen_by)) {
                $seenUserIds = array_keys($chatMessage->seen_by);

                $seenUserNames = User::whereIn('id', $seenUserIds)
                    ->pluck('name')
                    ->implode(', ');
            }

            return [
                'id' => $chatMessage->id,
                'group_name' => optional($chatMessage->group)->name ?? 'N/A',
                'sender_name' => optional($chatMessage->sender)->name ?? 'N/A',
                'event' => optional($chatMessage->group?->event)->name ?? 'N/A',
                'message' => $chatMessage->message,
                'seen_by' => $seenUserNames,

                'created_at' => $chatMessage->created_at->format('d M Y'),
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
        $groupId = $request->get('group_id');

        $query = Messages::with(['sender', 'group.event'])
            ->when(!$isAdmin, function ($q) use ($user) {
                $q->whereHas('sender', function ($sq) use ($user) {
                    $sq->where('event_id', $user->event_id);
                });
            })
            ->when($search, function ($q) use ($search) {
                $q->where(function ($q) use ($search) {
                    $q->where('message', 'like', "%{$search}%")
                        ->orWhereHas('sender', function ($sq) use ($search) {
                            $sq->where('name', 'like', "%{$search}%")
                                ->orWhere('email', 'like', "%{$search}%");
                        })
                        ->orWhereHas('group', function ($gq) use ($search) {
                            $gq->where('name', 'like', "%{$search}%");
                        });
                });
            })
            ->when($groupId, function ($q) use ($groupId) {
                $q->where('group_id', $groupId);
            })
            ->orderBy('id', 'desc')
            ->get();

        $headers = $isAdmin
            ? ['Event', 'Group', 'Sender', 'Message', 'Seen By', 'Date']
            : ['Group', 'Sender', 'Message', 'Seen By', 'Date'];

        $rows = [];
        $rows[] = implode(',', $headers);

        $esc = fn($val) => '"' . str_replace('"', '""', $val) . '"';

        foreach ($query as $msg) {
            $seenUserNames = 'Not seen';
            if (!empty($msg->seen_by)) {
                $seenUserIds = array_keys($msg->seen_by);
                $seenUserNames = User::whereIn('id', $seenUserIds)->pluck('name')->implode(', ');
            }

            $eventName = optional($msg->group?->event)->name ?? 'N/A';
            $groupName = optional($msg->group)->name ?? 'N/A';
            $senderName = optional($msg->sender)->name ?? 'N/A';
            $message = $msg->message ?? 'N/A';
            $date = $msg->created_at?->format('d M Y') ?? 'N/A';

            $row = $isAdmin
                ? [$esc($eventName), $esc($groupName), $esc($senderName), $esc($message), $esc($seenUserNames), $esc($date)]
                : [$esc($groupName), $esc($senderName), $esc($message), $esc($seenUserNames), $esc($date)];

            $rows[] = implode(',', $row);
        }

        $csv = implode("\n", $rows);
        $filename = 'chat_messages_export_' . now()->format('Y-m-d') . '.csv';

        return response($csv, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }
}
