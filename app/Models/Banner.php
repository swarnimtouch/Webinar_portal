<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Banner extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'filename',
        'video_url',
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
        if ($this->type === 'video' && $this->video_url) {
            return $this->video_url;
        }

        return asset(
            $this->filename
                ? 'storage/banners/' . $this->filename
                : 'assets/media/avatars/blank.png'
        );
    }

    public function getSliderDataAttribute()
    {
        $data = [
            'type' => $this->type ?? 'image',
            'src' => $this->media_url,
        ];

        if ($this->type === 'video' && !empty($this->poster)) {
            $data['poster'] = asset('storage/banners/' . $this->poster);
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
