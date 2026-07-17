<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('event_resources', function (Blueprint $table) {
            $table->string('resource_type', 20)->default('file')->after('title');
            $table->text('url')->nullable()->after('resource_type');
            $table->string('file_path')->nullable()->change();
            $table->string('original_name')->nullable()->change();
        });
    }

    public function down(): void
    {
        DB::table('event_resources')->whereNull('file_path')->update(['file_path' => '']);
        DB::table('event_resources')->whereNull('original_name')->update(['original_name' => '']);

        Schema::table('event_resources', function (Blueprint $table) {
            $table->dropColumn(['resource_type', 'url']);
            $table->string('file_path')->nullable(false)->change();
            $table->string('original_name')->nullable(false)->change();
        });
    }
};
