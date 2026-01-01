<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AddDropdownInputSeeder extends Seeder
{
    public function run()
    {
        DB::table('attribute_inputs')->insertOrIgnore([
            'input_name' => 'Dropdown Select',
            'icon' => 'bi bi-caret-down-square',
            'status' => 'active',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
