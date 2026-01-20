<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('user_attendences', function (Blueprint $table) {

            $table->dateTime('joined_at')
                ->nullable()
                ->after('session_time');

            $table->dateTime('last_ping_at')
                ->nullable()
                ->after('joined_at');
        });
    }

    public function down(): void
    {
        Schema::table('user_attendences', function (Blueprint $table) {
            $table->dropColumn(['joined_at', 'last_ping_at']);
        });
    }
};

