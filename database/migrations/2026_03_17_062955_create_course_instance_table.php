<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('course_instance', function (Blueprint $table) {
            $table->id('course_instance_id');

            $table->unsignedBigInteger('course_template_id');

            $table->unsignedBigInteger('level_id')->nullable();
            $table->unsignedBigInteger('sublevel_id')->nullable();

            $table->unsignedBigInteger('patch_id');
            $table->unsignedBigInteger('teacher_id');
            $table->unsignedBigInteger('branch_id');

            $table->date('start_date');
            $table->date('end_date');

            $table->unsignedBigInteger('room_id')->nullable();

            $table->enum('delivery_mood', ['Offline','Online']);

            $table->decimal('total_hours', 6, 2);
            $table->decimal('session_duration', 4, 2);

            $table->integer('capacity');

            $table->enum('type', ['Private','Group'])->nullable();

            $table->enum('status', [
                'Upcoming','Active','Completed','Cancelled'
            ])->default('Upcoming');

            $table->unsignedBigInteger('created_by_employee_id')->nullable();

            $table->timestamps();

            $table->index('course_template_id');
            $table->index('patch_id');
            $table->index('teacher_id');
            $table->index('branch_id');

            $table->foreign('course_template_id')
                ->references('course_template_id')
                ->on('course_template')
                ->restrictOnDelete();

            $table->foreign('level_id')
                ->references('level_id')
                ->on('level')
                ->nullOnDelete();

            $table->foreign('sublevel_id')
                ->references('sublevel_id')
                ->on('sublevel')
                ->nullOnDelete();

            $table->foreign('patch_id')
                ->references('patch_id')
                ->on('patch')
                ->restrictOnDelete();

            $table->foreign('teacher_id')
                ->references('teacher_id')
                ->on('teacher')
                ->restrictOnDelete();

            $table->foreign('room_id')
                ->references('room_id')
                ->on('room')
                ->nullOnDelete();

            $table->foreign('branch_id')
                ->references('branch_id')
                ->on('branch')
                ->restrictOnDelete();

            $table->foreign('created_by_employee_id')
                ->references('employee_id')
                ->on('employee')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('course_instance');
    }
};
