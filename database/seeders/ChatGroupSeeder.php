<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ChatGroupSeeder extends Seeder
{
    public function run()
    {
        DB::table('chat_groups')->insert([
            'name'       => 'Laravel Developers',
            'created_by' => 1,
            'members'    => json_encode([1,2,3,4]),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}

