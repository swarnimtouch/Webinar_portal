<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserDynamicFieldValue extends Model
{
    protected $fillable = ['user_id', 'dynamic_field_id', 'value'];

    public function field()
    {
        return $this->belongsTo(DynamicFields::class, 'dynamic_field_id');
    }
}
