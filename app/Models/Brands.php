<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Brands extends Model
{
    //
    use HasFactory;

    protected $fillable = [
        'title',
        'filename',
        'type',
        'status'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
    public function getMediaUrlAttribute()
    {
        return $this->filename
            ? asset('storage/brands/' . $this->filename)
            : null;
    }

}
