<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Poll;
use App\Models\User;

class UserQuizAnswer extends Model
{
    //
        protected $fillable = ['poll_id', 'user_id', 'answer'];
public function poll()
{
    return $this->belongsTo(Poll::class);
}

// User relation
public function user()
{
    return $this->belongsTo(User::class);
}
}
