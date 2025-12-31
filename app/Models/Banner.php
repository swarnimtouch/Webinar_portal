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

    // 🔹 Auto include in array / json
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
                : 'assets/media/no-image.png'
        );
    }


    /**
     * SET Attribute (Mutator)
     * $banner->filename = $file
     */
    public function setFilenameAttribute($file)
    {
        // agar already string aayi ho (edit case)
        if (is_string($file)) {
            $this->attributes['filename'] = $file;
            return;
        }

        // agar uploaded file hai
        if ($file instanceof \Illuminate\Http\UploadedFile) {
            $name = time().'_'.$file->getClientOriginalName();
            $file->storeAs('public/banners', $name);
            $this->attributes['filename'] = $name;
        }
    }
}
