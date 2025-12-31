<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DaynamicFields extends Model
{
    protected $table = 'daynamic_fields';

    protected $fillable = [
        'index_no',
        'field_name',
        'label',
        'input_type',
        'input_value',
        'html_class',
        'is_required',
        'type',
        'status',
        'is_profile_field',
        'login_with'
    ];

}
