<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Content extends Model
{
    //
    protected  $fillable = [
        'title',
        'slug',
        'content',
        'event_id',
    ];

    public function event()
    {
        return $this->belongsTo(Events::class, 'event_id');
    }
}
