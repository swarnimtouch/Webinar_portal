<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Messages extends Model
{
    use HasFactory;

    protected $table = 'messages';

    protected $fillable = [
        'name',
        'thread_id',
        'sender_id',
        'message',
        'created_by',
        'members',
    ];

    protected $casts = [
        'members' => 'array',
        'seen_by' => 'array',
    ];

    /**
     * Group creator (Admin/User)
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function group()
    {
        return $this->belongsTo(Thread::class, 'group_id');
    }

    /**
     * Group messages
     */
    public function messages()
    {
        return $this->hasMany(Messages::class, 'group_id');
    }
}
