<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('instance_schedule', function (Blueprint $table) {
            $table->id('instance_schedule_id');

            $table->unsignedBigInteger('course_instance_id');

            $table->enum('day_of_week', [
                'sun_wed','sat_tue', 'mon_thu'
            ])->nullable();

            $table->unsignedBigInteger('time_slot_id')->nullable();

            $table->unsignedBigInteger('created_by_employee_id')->nullable();

            $table->timestamps();

            $table->index('course_instance_id');

            $table->unique(
                ['course_instance_id', 'day_of_week', 'time_slot_id'],
                'unique_schedule_per_instance'
            );

            $table->foreign('course_instance_id')
                ->references('course_instance_id')
                ->on('course_instance')
                ->cascadeOnDelete();

            $table->foreign('time_slot_id')
                ->references('time_slot_id')
                ->on('time_slot')
                ->nullOnDelete();

            $table->foreign('created_by_employee_id')
                ->references('employee_id')
                ->on('employee')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('instance_schedule');
    }
};