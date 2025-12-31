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
            ['input_name' => 'Short Text (up to 70 characters)'],
            ['input_name' => 'Long Text (up to 300 characters)'],
            ['input_name' => 'Single Select Answer'],
            ['input_name' => 'Multi Select Answer'],
            ['input_name' => 'Date Field'],
            ['input_name' => 'File Upload'],
            ['input_name' => 'Password'],
            ['input_name' => 'Login With'],
            ['input_name' => 'Check boxes'],
            ['input_name' => 'Consent'],
            ['input_name' => 'Radio buttons'],
            ['input_name' => 'Date and Time Field'],
        ];

        foreach ($inputs as $input) {
            DB::table('attribute_inputs')->insert([
                'input_name' => $input['input_name'],
                'status'     => 'active',
                'created_at'=> $now,
                'updated_at'=> $now,
            ]);
        }
    }
}
