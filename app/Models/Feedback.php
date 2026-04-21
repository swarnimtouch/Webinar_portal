<?php

namespace App\Models;

use App\Models\User;


use Illuminate\Database\Eloquent\Model;

class Feedback extends Model
{
    protected $fillable = [
        'event_id',
        'user_id',
        'rating',
        'comment',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function event()
    {
        return $this->belongsTo(Events::class);
    }

}
