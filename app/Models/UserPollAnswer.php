<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Poll;
use App\Models\User;

class UserPollAnswer extends Model
{
    protected $fillable = ['poll_id', 'user_id', 'answer', 'answer_id'];

    public function poll()
    {
        return $this->belongsTo(Poll::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
