<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Speakers extends Model
{
    //
    protected $fillable = [
        'filename',
        'name',
        'line1',
        'line2',
        'line3',
        'status'
    ];
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
    public function getMediaUrlAttribute()
    {
        return $this->filename
            ? asset('storage/speakers/' . $this->filename)
            : null;
    }

    public function getStatusBadgeAttribute()
    {
        return $this->status === 'active'
            ? '<span class="badge badge-light-success">Active</span>'
            : '<span class="badge badge-light-danger">Inactive</span>';
    }
}
