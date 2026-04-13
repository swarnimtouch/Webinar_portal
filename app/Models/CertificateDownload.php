<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CertificateDownload extends Model
{
    protected $table = 'certificate_downloads';

    protected $fillable = [
        'certificate_id',
        'user_id',
        'file_path',
    ];

    public function certificate()
    {
        return $this->belongsTo(Certificate::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
