<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
        return $this->hasMany(UserQuizAnswer::class);
    }

}
