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
        Schema::create('time_slot', function (Blueprint $table) {
            $table->id('time_slot_id');

            $table->string('name', 100);

            $table->time('start_time');
            $table->time('end_time');

            $table->enum('slot_type', ['Morning','Midday','Night']);

            $table->boolean('is_active')->default(true);

            $table->unsignedBigInteger('created_by_admin_id');

            $table->timestamps();

            $table->foreign('created_by_admin_id')
                ->references('employee_id')
                ->on('employee')
                ->cascadeOnDelete();

            $table->unique(['start_time', 'end_time']);

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('time_slot');
    }
};
