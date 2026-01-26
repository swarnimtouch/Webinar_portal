<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HomeSetting extends Model
{
    //
    protected $fillable=[
        'title',
        'player_id',
        'player_type',
        'url',
        'publish_date',
        'about_us',
        'event_start_time',
        'event_end_time',
        'active_from_date',
        'active_to_date',
        'user_attendance'
    ];
    protected $casts = [
        'publish_date' => 'date',
        'event_start_time'=> 'datetime',
        'event_end_time'=> 'datetime',
        'active_from_date'=> 'datetime',
        'active_to_date'=> 'datetime',

    ];

}
