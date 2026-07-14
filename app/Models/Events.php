<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Events extends Model
{
    protected $casts = [
        'publish_date' => 'date',
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'active_user_from' => 'datetime',
        'active_user_to' => 'datetime',
        'is_log_attendance' => 'boolean',
        'session_agenda' => 'array',
    ];

    public function getFaviconAttribute($value)
    {
        return !empty($value) ? asset('storage/events/' . $value) : asset('assets/media/no_image.png');
    }

    public function getLogoAttribute($value)
    {
        return !empty($value) ? asset('storage/events/' . $value) : asset('assets/media/no_image.png');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function resources(): HasMany
    {
        return $this->hasMany(EventResource::class, 'event_id');
    }

    public function getPublicUrlAttribute(): string
    {
        $companySlug = $this->company?->slug ?: $this->domain;
        $baseDomain = config('app.event_base_domain', 'doctorly.in');

        return "https://{$companySlug}.{$baseDomain}/{$this->slug}";
    }
}
