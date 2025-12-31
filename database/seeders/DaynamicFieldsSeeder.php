<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DaynamicFieldsSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        $fields = [
            [
                'index_no' => 1,
                'field_name' => 'first_name',
                'label' => 'First Name',
                'input_type' => 1, // Short Text
                'input_value' => null,
                'type' => 'default',
            ],
            [
                'index_no' => 2,
                'field_name' => 'last_name',
                'label' => 'Last Name',
                'input_type' => 1,
                'input_value' => null,
                'type' => 'default',
            ],
            [
                'index_no' => 3,
                'field_name' => 'email',
                'label' => 'Email',
                'input_type' => 1,
                'input_value' => null,
                'type' => 'default',
            ],
            [
                'index_no' => 4,
                'field_name' => 'mobile_number',
                'label' => 'Mobile Number',
                'input_type' => 1,
                'input_value' => null,
                'type' => 'default',
            ],
            [
                'index_no' => 5,
                'field_name' => 'country',
                'label' => 'Country',
                'input_type' => 3, // Single Select
                'input_value' => json_encode([
                    'source' => 'countries',   // table name
                    'value'  => 'id',
                    'label'  => 'name'
                ]),
                'type' => 'default',
            ],
            [
                'index_no' => 6,
                'field_name' => 'state',
                'label' => 'State',
                'input_type' => 3,
                'input_value' => json_encode([
                    'source'      => 'states',
                    'value'       => 'id',
                    'label'       => 'name',
                    'depends_on'  => 'country'
                ]),
                'type' => 'default',
            ],
            [
                'index_no' => 7,
                'field_name' => 'city',
                'label' => 'City',
                'input_type' => 3,
                'input_value' => json_encode([
                    'source'      => 'cities',
                    'value'       => 'id',
                    'label'       => 'name',
                    'depends_on'  => 'state'
                ]),
                'type' => 'default',
            ],
            [
                'index_no' => 8,
                'field_name' => 'password',
                'label' => 'Password',
                'input_type' => 7, // Password
                'input_value' => null,
                'type' => 'password',
            ],
        ];


        foreach ($fields as $field) {
            DB::table('daynamic_fields')->updateOrInsert(
                ['field_name' => $field['field_name']],
                array_merge($field, [
                    'html_class' => 'col-12',
                    'is_required' => 1,
                    'status' => 'active',
                    'is_profile_field' => 'no',
                    'created_at' => $now,
                    'updated_at' => $now,
                ])
            );
        }
    }
}

