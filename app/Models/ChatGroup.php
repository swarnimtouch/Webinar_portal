<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ChatGroup extends Model
{
    use HasFactory;

    protected $table = 'chat_groups';

    protected $fillable = [
        'name',
        'created_by',
        'members',
    ];

    protected $casts = [
        'members' => 'array', // JSON → array
    ];

    /**
     * Group creator (Admin/User)
     */
    public function creator()
    {
        return $this->belongsTo(\App\User::class, 'created_by');
    }

    /**
     * Group messages
     */
    public function messages()
    {
        return $this->hasMany(ChatMessage::class, 'group_id');
    }
}
