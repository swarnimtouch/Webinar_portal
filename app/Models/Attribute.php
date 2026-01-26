<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attribute extends Model
{

    protected $fillable = [
        'input_name',
        'status',
    ];


    public function dynamicFields()
    {
        return $this->hasMany(DynamicFields::class, 'attribute_id');
    }
}
