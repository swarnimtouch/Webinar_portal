<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Thread extends Model
{
    protected $table = 'threads';

    protected $fillable = [
        'name',
        'created_by',
        'members'
    ];

    protected $casts = [
        'members' => 'array',
    ];
}
