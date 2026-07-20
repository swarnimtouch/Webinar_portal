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
        Schema::table('dynamic_fields', function (Blueprint $table) {
            $table->unsignedBigInteger('event_id')->after('id')->nullable();
            $table->foreign('event_id')->references('id')->on('events')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('dynamic_fields', function (Blueprint $table) {
            $table->dropForeign('dynamic_fields_event_id_foreign');
            $table->dropColumn('event_id');
        });
    }
};
