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
        Schema::create('daynamic_fields', function (Blueprint $table) {
            $table->bigIncrements('id');


            $table->integer('index_no')->nullable();

            $table->string('field_name')->nullable();
            $table->string('label')->nullable();

            // foreign key reference to attribute_inputs table
            $table->unsignedBigInteger('input_type')->nullable()
                ->comment('attribute_input_id');

            $table->text('input_value')->nullable();
            $table->string('html_class')->nullable();

            $table->tinyInteger('is_required')->default(0)
                ->comment('0=not required,1=required');

            $table->enum('type', [
                'default',
                'custom',
                'password',
                'login'
            ])->default('custom');

            $table->enum('status', ['active', 'inactive'])
                ->default('active');

            $table->enum('is_profile_field', ['yes', 'no'])
                ->default('no')
                ->comment('custom field will add in profile form');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('daynamic_fields');
    }
};
