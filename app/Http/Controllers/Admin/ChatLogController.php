<?php

namespace App\Http\Controllers\Admin;

use App\Events\ChatMessage;
use App\Models\Events;
use App\Models\Thread;
use App\Models\Messages;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatLogController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        $eventIds = Thread::distinct()
            ->pluck('event_id')
            ->filter()
            ->unique()
            ->values();

        $groups = Events::whereIn('id', $eventIds)
            ->orderBy('name')
            ->get();

        if ($user->type === 'sub_admin') {
            $groups = $groups->where('id', $user->event_id)->values();
        }

        return view('admin.chat_log.index', [
            'title' => __('Chat Log'),
            'groups' => $groups,
            'breadcrumb' => breadcrumb([
                __('Chat Log') => route('admin.chat_log')
            ])
        ]);
    }

    public function datatable(Request $request)
    {
        $user = auth()->user();

        $query = Thread::with(['event']);

        if ($user->type === 'sub_admin') {
            $query->where('event_id', $user->event_id);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhereHas('event', function ($eq) use ($search) {
                        $eq->where('name', 'like', "%{$search}%");
                    });
            });
        }
        if ($request->filled('event')) {
            $query->where('event_id', $request->event);
        }

        $total = $query->count();

        if ($request->has('order')) {
            $columns = $request->columns;
            foreach ($request->order as $order) {
                $columnIndex = $order['column'];
                $columnName = $columns[$columnIndex]['data'];
                $direction = $order['dir'];

                $dbColumn = match ($columnName) {
                    'name' => 'name',
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

        $threads = $query->skip($start)->take($length)->get();

        $data = $threads->map(function ($thread) {
            return [
                'id' => $thread->id,
                'name' => $thread->name,
                'event' => optional($thread->event)->name ?? 'N/A',
                'created_at' => $thread->created_at?->format('d M Y') ?? 'N/A',
            ];
        });

        return response()->json([
            'draw' => (int)$request->input('draw'),
            'recordsTotal' => $total,
            'recordsFiltered' => $total,
            'data' => $data,
        ]);
    }

    public function threadMessages($threadId)
    {
        $user = auth()->user();
        $thread = Thread::with('event')->findOrFail($threadId);

        if ($user->type === 'sub_admin' && $thread->event_id !== $user->event_id) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $messages = Messages::with('sender')
            ->where('thread_id', $threadId)
            ->orderBy('created_at', 'asc')
            ->get()
            ->map(function ($msg) {
                return [
                    'id' => $msg->id,
                    'sender_name' => optional($msg->sender)->name ?? 'N/A',
                    'message' => $msg->message,
                    'created_at' => $msg->created_at?->format('d M Y, h:i A') ?? 'N/A',
                ];
            });

        return response()->json([
            'success' => true,
            'thread_name' => $thread->name,
            'messages' => $messages,
        ]);
    }

    public function delete($id)
    {
        try {
            $thread = Thread::findOrFail($id);

            Messages::where('thread_id', $id)->delete();
            $thread->delete();

            return response()->json([
                'success' => true,
                'message' => 'Thread deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting thread'
            ], 500);
        }
    }

    public function deleteMultiple(Request $request)
    {
        try {
            $ids = $request->input('ids', []);

            if (empty($ids)) {
                return response()->json([
                    'success' => false,
                    'message' => 'No threads selected'
                ], 400);
            }

            Messages::whereIn('thread_id', $ids)->delete();
            Thread::whereIn('id', $ids)->delete();

            return response()->json([
                'success' => true,
                'message' => 'Threads deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting threads'
            ], 500);
        }
    }

    public function export(Request $request)
    {
        $user = auth()->user();
        $isAdmin = $user->type === 'admin';
        $search = $request->get('search');

        $query = Thread::with(['event'])
            ->when(!$isAdmin, function ($q) use ($user) {
                $q->where('event_id', $user->event_id);
            })
            ->when($search, function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhereHas('event', function ($eq) use ($search) {
                        $eq->where('name', 'like', "%{$search}%");
                    });
            })
            ->orderBy('id', 'desc')
            ->get();

        $headers = $isAdmin
            ? ['Event', 'Thread Name', 'Created At']
            : ['Thread Name', 'Created At'];

        $rows = [];
        $rows[] = implode(',', $headers);
        $esc = fn($val) => '"' . str_replace('"', '""', $val) . '"';

        foreach ($query as $thread) {
            $eventName = optional($thread->event)->name ?? 'N/A';
            $threadName = $thread->name ?? 'N/A';
            $createdAt = $thread->created_at?->format('d M Y') ?? 'N/A';

            $row = $isAdmin
                ? [$esc($eventName), $esc($threadName), $esc($createdAt)]
                : [$esc($threadName), $esc($createdAt)];

            $rows[] = implode(',', $row);
        }

        $csv = implode("\n", $rows);
        $filename = 'threads_export_' . now()->format('Y-m-d') . '.csv';

        return response($csv, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }

    public function show($id)
    {
        $currentUser = auth()->user();
        $thread = Thread::with('event')->findOrFail($id);

        if ($currentUser->type === 'sub_admin' && $thread->event_id !== $currentUser->event_id) {
            abort(403);
        }

        $threadMessages = Messages::with('sender')
            ->where('thread_id', $id)
            ->orderBy('created_at', 'asc')
            ->get();

        $threadParticipants = $threadMessages->pluck('sender')->filter()->unique('id');

        return view('admin.chat_log.show', [
            'title' => __('Thread Messages'),
            'thread' => $thread,
            'messages' => $threadMessages,
            'participants' => $threadParticipants,
            'breadcrumb' => breadcrumb([
                __('Chat Log') => route('admin.chat_log'),
                $thread->name => '#',
            ])
        ]);
    }

    public function sendMessage(Request $request, $threadId)
    {
        $request->validate([
            'message' => 'required|string|max:5000',
        ]);

        $currentUser = auth()->user();

        $thread = Thread::with('event')->findOrFail($threadId);
        $event = $thread->event;

        if ($currentUser->type === 'sub_admin' && $thread->event_id !== $currentUser->event_id) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $newMessage = Messages::create([
            'thread_id' => $thread->id,
            'sender_id' => $currentUser->id,
            'message' => $request->input('message'),
        ]);

        $formattedDate = $newMessage->created_at->format('H:i A');

        broadcast(new ChatMessage(
            message: $newMessage->message,
            userName: $currentUser->name,
            userId: $currentUser->id,
            timestamp: $formattedDate,
            slug: $event->slug
        ));

        return response()->json([
            'success' => true,
            'message' => 'Message sent',
        ]);
    }


}
