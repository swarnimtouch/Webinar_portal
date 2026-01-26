<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DynamicFields extends Model
{

    protected $fillable = [
        'index_no',
        'field_name',
        'label',
        'attribute_id',
        'input_value',
        'html_class',
        'is_required',
        'type',
        'status',
        'is_profile_field',
        'login_with'
    ];
    public function attributeInput()
    {
        return $this->belongsTo(Attribute::class, 'attribute_id');
    }

}
