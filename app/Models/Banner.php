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
    public function getMediaUrlAttribute()
    {
        return asset(
            $this->filename
                ? 'storage/banners/' . $this->filename
                : 'assets/media/avatars/blank.png'
        );
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
            $name = time().'_'.$file->getClientOriginalName();
            $file->storeAs('public/banners', $name);
            $this->attributes['filename'] = $name;
        }
    }
}
