<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AttributeInputSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        $inputs = [
            ['name' => 'Short Text (up to 70 characters)', 'type' => 'text'],
            ['name' => 'Long Text (up to 300 characters)', 'type' => 'textarea'],
            ['name' => 'Single Select Answer', 'type' => 'select'],
            ['name' => 'Multi Select Answer', 'type' => 'checkbox'],
            ['name' => 'Date Field', 'type' => 'date'],
            ['name' => 'File Upload', 'type' => 'file'],
            ['name' => 'Password', 'type' => 'password'],
            ['name' => 'Login With', 'type' => 'radio'],
            ['name' => 'Check boxes', 'type' => 'checkbox'],
            ['name' => 'Consent', 'type' => 'checkbox'],
            ['name' => 'Radio buttons', 'type' => 'radio'],
            ['name' => 'Date and Time Field', 'type' => 'date'],
        ];

        foreach ($inputs as $input) {
            DB::table('attributes')->insert([
                'name' => $input['name'],
                'type' => $input['type'],
                'status' => 'active',
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }
}
