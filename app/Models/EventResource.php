<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EventResource extends Model
{
    protected $fillable = [
        'event_id',
        'slot',
        'title',
        'file_path',
        'original_name',
    ];

    public function event(): BelongsTo
    {
        return $this->belongsTo(Events::class, 'event_id');
    }
}
