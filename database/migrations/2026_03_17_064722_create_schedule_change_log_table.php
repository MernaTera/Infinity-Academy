<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('schedule_change_log', function (Blueprint $table) {
            $table->id('change_id');

            $table->unsignedBigInteger('course_instance_id');

            $table->json('old_schedule');
            $table->json('new_schedule');

            $table->date('effective_from');

            $table->unsignedBigInteger('changed_by_employee_id');

            $table->timestamps();

            $table->foreign('course_instance_id')
                ->references('course_instance_id')
                ->on('course_instance')
                ->cascadeOnDelete();

            $table->foreign('changed_by_employee_id')
                ->references('employee_id')
                ->on('employee')
                ->restrictOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('schedule_change_log');
    }
};