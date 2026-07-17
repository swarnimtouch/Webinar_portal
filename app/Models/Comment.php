<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    protected $fillable = ['event_id', 'user_id', 'comment', 'is_approved'];
    protected $casts = ['is_approved' => 'boolean'];
    public function user() { return $this->belongsTo(User::class); }
    public function event() { return $this->belongsTo(Events::class); }
    public function votes() { return $this->hasMany(CommentVote::class); }
}
