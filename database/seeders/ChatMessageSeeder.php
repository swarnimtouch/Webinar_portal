<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ChatMessageSeeder extends Seeder
{
    public function run()
    {
        DB::table('messages')->insert([
            'group_id' => 1,
            'sender_id' => 1,
            'message' => 'Hello everyone 👋',
            'seen_by' => json_encode([
                "2" => "2026-02-03 10:30:00",
                "3" => "2026-02-03 10:32:00"
            ]),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}

