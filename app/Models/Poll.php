<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Poll extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'question',
        'answer',
        'status',
        'is_hidden'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'is_hidden' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * Scope for active polls
     */
    public function scopeActiveVisibleLatest($query)
    {
        return $query->where('status', 'active')
            ->where('is_hidden', 0)
            ->orderBy('id', 'desc');
    }

    public function votes()
    {
        return $this->hasMany(UserPollAnswer::class);
    }

    public function poll_answers()
    {
        return $this->hasMany(PollAnswer::class);
    }

    public function event()
    {
        return $this->belongsTo(Events::class);
    }

    public function scopeCurrentEvent($query)
    {
        $event = app('event');
        return $query->where('event_id', $event->id ?? 0);
    }

}
