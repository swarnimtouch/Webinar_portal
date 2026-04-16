<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Events extends Model
{
    protected $casts = [
        'publish_date' => 'date',
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'active_user_from' => 'datetime',
        'active_user_to' => 'datetime',
    ];

    public function getFaviconAttribute($value)
    {
        return !empty($value) ? asset('storage/events/' . $value) : asset('assets/media/no_image.png');
    }

    public function getLogoAttribute($value)
    {
        return !empty($value) ? asset('storage/events/' . $value) : asset('assets/media/no_image.png');
    }
}
