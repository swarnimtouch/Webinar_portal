<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'type',
        'status',
        'event_id',
        'first_name',
        'last_name',
        'name',
        'username',
        'email',
        'email_verified_at',
        'password',
        'mobile',
        'alternative_mobile',
        'address',
        'city',
        'state',
        'country',
        'speciality',
        'super_speciality',
        'hospital',
        'therapy',
        'avatar',
        'remember_token',
        'created_at'
    ];


    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * The attributes that should have default values.
     *
     * @var array
     */
    protected $attributes = [
        'type' => 'doctor', // Default type set to doctor
    ];

    /**
     * Boot method to set default values
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($user) {
            if (empty($user->type)) {
                $user->type = 'doctor';
            }
        });
    }

    public function getAvatarAttribute($value): string
    {
        return (!empty($value)) ? asset("storage/" . $value) : asset('assets/media/avatars/blank.png');
    }

    public function event()
    {
        return $this->belongsTo(Events::class);

    }

    public function attendances()
    {
        return $this->hasMany(UserAttendance::class, 'user_id');
    }

    public function dynamicFieldValues()
    {
        return $this->hasMany(UserDynamicFieldValue::class);
    }

    public function dynamicValue(DynamicFields $field): mixed
    {
        $mapping = ['mobile_number' => 'mobile', 'alternative_mobile_number' => 'alternative_mobile'];
        $column = $mapping[$field->field_name] ?? $field->field_name;

        if (array_key_exists($column, $this->getAttributes())) {
            return $this->getAttribute($column);
        }

        return $this->dynamicFieldValues->firstWhere('dynamic_field_id', $field->id)?->value;
    }
}
