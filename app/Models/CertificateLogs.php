<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CertificateLogs extends Model
{
    protected $table = 'certificate_logs';

    protected $fillable = [
        'certificate_id',
        'user_id',
        'file_path',
    ];

    public function scopeLatestPerUserCertificate($query)
    {
        return $query->whereIn('id', static::query()
            ->selectRaw('MAX(id)')
            ->groupBy('user_id', 'certificate_id'));
    }

    public function certificate()
    {
        return $this->belongsTo(Certificate::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
