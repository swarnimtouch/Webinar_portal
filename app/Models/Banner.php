<?php

namespace App\Models;

use App\Support\EventStorage;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Banner extends Model
{
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

    protected $appends = ['media_url'];

    /**
     * GET Attribute (Accessor)
     * $banner->media_url
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function getMediaUrlAttribute()
    {
        return $this->filename
            ? EventStorage::url($this->filename, 'banners/' . $this->filename)
            : asset('assets/media/avatars/blank.png');
    }

    public function getSliderDataAttribute()
    {
        $data = [
            'type' => $this->type ?? 'image',
            'src' => EventStorage::url($this->filename, 'banners/' . $this->filename),
        ];

        if ($this->type === 'video' && !empty($this->poster)) {
            $data['poster'] = EventStorage::url($this->poster, 'banners/' . $this->poster);
        }

        return $data;
    }

    /**
     * SET Attribute (Mutator)
     * $banner->filename = $file
     */
    public function setFilenameAttribute($file)
    {
        if (is_string($file)) {
            $this->attributes['filename'] = $file;
            return;
        }

        if ($file instanceof \Illuminate\Http\UploadedFile) {
            $name = time() . '_' . $file->getClientOriginalName();
            $file->storeAs('public/banners', $name);
            $this->attributes['filename'] = $name;
        }
    }

    public function event()
    {
        return $this->belongsTo(Events::class);
    }
}
