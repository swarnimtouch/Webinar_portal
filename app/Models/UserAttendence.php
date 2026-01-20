<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserAttendence extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'session_time'
    ];

    protected $casts = [
        'session_time' => 'integer'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
