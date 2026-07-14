<?php

namespace App\Http\Controllers\Website;

use App\Events\ChatMessage;
use App\Events\UserRaisedHand;
use App\Models\Messages;
use App\Models\Thread;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class ChatController
{
    private mixed $event = null;

    public function __construct()
    {
        $this->event = app('event');
    }

    public function getMessages(Request $request)
    {
        $thread = Thread::where('event_id', $this->event->id)->first();
        $messages = collect([]);

        if ($thread) {
            $messages = Messages::with('sender')
                ->where('thread_id', $thread->id)
                ->latest()
                ->take(50)
                ->get()
                ->reverse()
                ->map(fn($m) => [
                    'id' => $m->id,
                    'message' => $m->message,
                    'userName' => $m->sender->name ?? 'Anonymous',
                    'userId' => $m->sender_id,
                    'timestamp' => $m->created_at->format('H:i'),
                ]);
        }

        return response()->json(['messages' => array_values($messages->toArray())]);
    }

    public function sendMessage(Request $request)
    {
        $request->validate(['message' => 'required|string|max:1000']);
        $slug = $this->event->slug;

        $thread = Thread::where('event_id', $this->event->id)->first();

        if (!$thread) {
            $thread = new Thread();
            $thread->event_id = $this->event->id;
            $thread->name = $this->event->name . ' - ' . $slug;
            $thread->created_by = Auth::guard('web')->id();
            $thread->members = json_encode([Auth::guard('web')->id()]);
            $thread->save();
        } else {
            $existing = json_decode($thread->members, true) ?? [];
            $thread->members = json_encode(array_unique(array_merge($existing, [Auth::guard('web')->id()])));
            $thread->save();
        }

        $msg = Messages::create([
            'thread_id' => $thread->id,
            'sender_id' => Auth::guard('web')->id(),
            'message' => $request->message,
        ]);

        $broadcastEvent = new ChatMessage(
            message: $msg->message,
            userName: Auth::guard('web')->user()->name,
            userId: Auth::guard('web')->id(),
            timestamp: $msg->created_at->format('H:i'),
            slug: $slug,
            id: $msg->id,
            senderType: 'user'
        );

        Log::info('Chat broadcast dispatching', [
            'channel' => "webinar.{$slug}.chat",
            'event' => $broadcastEvent->broadcastAs(),
            'payload' => get_object_vars($broadcastEvent),
        ]);
        broadcast($broadcastEvent)->toOthers();

        return response()->json([
            'status' => true,
            'message' => [
                'id' => $msg->id,
                'message' => $msg->message,
                'userName' => Auth::guard('web')->user()->name,
                'userId' => Auth::guard('web')->id(),
                'timestamp' => $msg->created_at->format('H:i'),
            ],
        ]);
    }

    public function raiseHand(Request $request)
    {
        $slug = $this->event->slug;
        $userId = Auth::id();
        $cacheKey = "hand_raised_{$slug}_{$userId}";
        $current = Cache::get($cacheKey, false);
        $raised = !$current;
        Cache::put($cacheKey, $raised, now()->addHours(2));

        broadcast(new UserRaisedHand(
            userId: $userId,
            userName: Auth::user()->name,
            raised: $raised,
            slug: $slug
        ));

        return response()->json(['status' => true, 'raised' => $raised]);
    }

    public function handStatus()
    {
        $slug = $this->event->slug;
        $cacheKey = "hand_raised_{$slug}_" . Auth::id();
        return response()->json(['raised' => Cache::get($cacheKey, false)]);
    }
}
