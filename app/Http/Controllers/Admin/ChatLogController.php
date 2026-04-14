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

        return view('admin.chatlog.index', [
            'title' => __('Chat Log'),
            'breadcrumb' => breadcrumb([
                __('Chat Log') => route('admin.chatlog')
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
                'message' => 'Chat message deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting chat message'
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
                    'message' => 'No chat messages selected'
                ], 400);
            }

            Messages::whereIn('id', $ids)->delete();

            return response()->json([
                'success' => true,
                'message' => 'Chat messages deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting chat messages'
            ], 500);
        }
    }

    /**
     * Datatable for chat messages (Admin)
     */
    public function datatable(Request $request)
    {
        $query = Messages::with(['sender', 'group']);

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
}
