<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('dynamic_fields', function (Blueprint $table) {
            $table->string('icon')->nullable()->after('html_class');
        });
    }

    public function down(): void
    {
        Schema::table('dynamic_fields', function (Blueprint $table) {
            $table->dropColumn('icon');
        });
    }
};
