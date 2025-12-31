<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('daynamic_fields', function (Blueprint $table) {
            // 1 = selected login field, 0 = not selected
            $table->tinyInteger('login_with')
                ->default(0)
                ->comment('1 = login field, 0 = normal field')
                ->after('status');
        });


    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('daynamic_fields', function (Blueprint $table) {
            //
        });
    }
};
