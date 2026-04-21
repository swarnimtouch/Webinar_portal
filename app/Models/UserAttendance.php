<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserAttendance extends Model
{


    use HasFactory;

    protected $fillable = [
        'user_id',
        'session_time',
        'joined_at',
        'last_ping_at',

    ];

    protected $casts = [
        'session_time' => 'integer'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
