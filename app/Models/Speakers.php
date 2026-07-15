<?php

namespace App\Models;

use App\Support\EventStorage;
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

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function getMediaUrlAttribute()
    {
        return $this->filename
            ? EventStorage::url($this->filename, 'speakers/' . $this->filename)
            : null;
    }

    public function getImageUrlAttribute()
    {
        return $this->filename
            ? EventStorage::url($this->filename, 'speakers/' . $this->filename)
            : asset('assets/images/default-user.png');
    }

    public function getStatusBadgeAttribute()
    {
        return $this->status === 'active'
            ? '<span class="badge badge-light-success">Active</span>'
            : '<span class="badge badge-light-danger">Inactive</span>';
    }

    public function event()
    {
        return $this->belongsTo(Events::class);
    }
}
