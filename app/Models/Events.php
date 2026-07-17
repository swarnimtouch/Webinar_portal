<?php

namespace App\Models;

use App\Support\EventStorage;
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
        'allow_sub_admin_settings' => 'boolean',
        'show_country_report' => 'boolean',
        'show_state_report' => 'boolean',
        'show_city_report' => 'boolean',
        'show_live_users' => 'boolean',
        'enable_live_chat' => 'boolean',
        'enable_comments' => 'boolean',
        'enable_polls' => 'boolean',
        'enable_feedback' => 'boolean',
        'session_agenda' => 'array',
    ];

    public function getFaviconAttribute($value)
    {
        return !empty($value) ? EventStorage::url($value, 'events/' . $value) : asset('storage/site_settings/favicon.png');
    }

    public function getLogoAttribute($value)
    {
        return !empty($value) ? EventStorage::url($value, 'events/' . $value) : asset('storage/site_settings/favicon.png');
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
        $baseDomain = config('app.event_base_domain', 'doctorly.in');
        $liveSubdomain = config('app.event_live_subdomain', 'live');

        return "https://{$liveSubdomain}.{$baseDomain}/{$this->slug}";
    }
}
