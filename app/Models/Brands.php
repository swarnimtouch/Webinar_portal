<?php

namespace App\Models;

use App\Support\EventStorage;
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

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function getMediaUrlAttribute()
    {
        return $this->filename
            ? EventStorage::url($this->filename, 'brands/' . $this->filename)
            : null;
    }

    public function getImageUrlAttribute()
    {
        return $this->filename
            ? EventStorage::url($this->filename, 'brands/' . $this->filename)
            : asset('assets/images/default-brand.png');
    }

    public function event()
    {
        return $this->belongsTo(Events::class);
    }
}
