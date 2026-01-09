<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AttributeInput extends Model
{
    protected $table = 'attribute_inputs';

    protected $fillable = [
        'input_name',
        'status',
    ];


    public function dynamicFields()
    {
        return $this->hasMany(DaynamicFields::class, 'input_type');
    }
}
