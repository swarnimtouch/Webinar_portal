<?php

namespace App\Http\Controllers\Website;

use App\Models\Messages;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatController
{
    /**
     * Get latest chat messages (polling)
     */
    public function fetchMessages(Request $request)
    {
        $groupId = 1;
        $userId = Auth::id();

        $messages = Messages::with('sender')
            ->where('group_id', $groupId)
            ->orderBy('id', 'asc')
            ->limit(50)
            ->get();


        foreach ($messages as $message) {

            if ($message->sender_id == $userId) {
                continue;
            }

            $seenBy = $message->seen_by ?? [];

            if (!array_key_exists($userId, $seenBy)) {
                $seenBy[$userId] = now()->toDateTimeString();

                $message->update([
                    'seen_by' => $seenBy
                ]);
            }
        }

        return response()->json(
            $messages->map(function ($msg) {
                return [
                    'id' => $msg->id,
                    'user' => optional($msg->sender)->name ?? 'Guest',
                    'message' => $msg->message,
                ];
            })
        );
    }


    /**
     * Send chat message
     */
    public function sendMessage(Request $request)
    {

        $request->validate([
            'message' => 'required|string|max:1000'
        ]);

        Messages::create([
            'group_id' => 1,
            'sender_id' => Auth::id(),
            'message' => $request->message,
        ]);

        return response()->json(['success' => true]);
    }
}
