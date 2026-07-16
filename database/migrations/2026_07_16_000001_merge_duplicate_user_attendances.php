<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        DB::table('user_attendances')
            ->orderBy('id')
            ->get()
            ->groupBy('user_id')
            ->each(function ($rows) {
                $primary = $rows->first();
                $duplicateIds = $rows->skip(1)->pluck('id');

                DB::table('user_attendances')->where('id', $primary->id)->update([
                    'session_time' => $rows->sum('session_time'),
                    'joined_at' => $rows->pluck('joined_at')->filter()->min(),
                    'last_ping_at' => null,
                    'updated_at' => now(),
                ]);

                if ($duplicateIds->isNotEmpty()) {
                    DB::table('user_attendances')->whereIn('id', $duplicateIds)->delete();
                }
            });

        Schema::table('user_attendances', function (Blueprint $table) {
            $table->unique('user_id', 'user_attendances_user_id_unique');
        });
    }

    public function down(): void
    {
        Schema::table('user_attendances', function (Blueprint $table) {
            $table->dropUnique('user_attendances_user_id_unique');
        });
    }
};
