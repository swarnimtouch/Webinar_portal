<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PollAnswer extends Model
{
    protected $guarded = [];

    public function poll()
    {
        return $this->belongsTo(Poll::class);
    }

    public function user_voted()
    {
        return $this->hasMany(UserPollAnswer::class,'answer_id','id');
    }
}
