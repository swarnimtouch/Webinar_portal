<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->boolean('allow_sub_admin_settings')->default(false)->after('is_log_attendance');
            $table->boolean('show_country_report')->default(false)->after('allow_sub_admin_settings');
            $table->boolean('show_state_report')->default(false)->after('show_country_report');
            $table->boolean('show_city_report')->default(false)->after('show_state_report');
            $table->boolean('show_live_users')->default(false)->after('show_city_report');
            $table->boolean('enable_live_chat')->default(true)->after('show_live_users');
            $table->boolean('enable_comments')->default(false)->after('enable_live_chat');
            $table->boolean('enable_polls')->default(true)->after('enable_comments');
            $table->boolean('enable_feedback')->default(true)->after('enable_polls');
        });
    }

    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->dropColumn([
                'allow_sub_admin_settings',
                'show_country_report',
                'show_state_report',
                'show_city_report',
                'show_live_users',
                'enable_live_chat',
                'enable_comments',
                'enable_polls',
                'enable_feedback',
            ]);
        });
    }
};
