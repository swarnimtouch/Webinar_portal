<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\User;        // 🔥 THIS LINE WAS MISSING
use App\Models\ChatGroup;


class ChatMessage extends Model
{
    use HasFactory;

    protected $table = 'chat_messages';

    protected $fillable = [
        'group_id',
        'sender_id',
        'message',
        'seen_by',
        'sender_status',
        'receiver_status',
    ];

    protected $casts = [
        'seen_by' => 'array', // JSON → array
        'sender_status' => 'boolean',
        'receiver_status' => 'boolean',
    ];

    /**
     * Message sender
     */
    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    /**
     * Message belongs to group
     */
    public function group()
    {
        return $this->belongsTo(ChatGroup::class, 'group_id');
    }
}
