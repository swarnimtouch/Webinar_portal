<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Certificate extends Model
{
    protected $table = 'certificates';

    protected $fillable = [
        'name',
        'background_image',
        'font_file',
        'font_size',
        'font_color',
        'is_bold',
        'start_x',
        'end_x',
        'y',
        'status'
    ];

    protected $casts = [
        'font_size' => 'integer',
        'is_bold' => 'boolean',
        'start_x' => 'integer',
        'end_x' => 'integer',
        'y' => 'integer',
    ];

    public function event()
    {
        return $this->belongsTo(Events::class);
    }
}
