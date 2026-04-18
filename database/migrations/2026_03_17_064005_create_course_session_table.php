<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('course_session', function (Blueprint $table) {
            $table->id('course_session_id');

            $table->unsignedBigInteger('course_instance_id');

            $table->date('session_date')->nullable();

            $table->dateTime('start_time')->nullable();
            $table->dateTime('end_time')->nullable();

            $table->integer('session_number')->nullable();

            $table->unsignedBigInteger('room_id')->nullable();

            $table->boolean('generated_from_schedule')->default(true);

            $table->enum('status', [
                'Scheduled','Completed','Cancelled'
            ])->default('Scheduled');

            $table->timestamps();

            $table->index('course_instance_id');

            $table->unique(
                ['room_id', 'session_date', 'start_time'],
                'unique_session_time'
            );

            $table->unique(
                ['course_instance_id', 'session_number'],
                'unique_session_number'
            );

            $table->foreign('course_instance_id')
                ->references('course_instance_id')
                ->on('course_instance')
                ->cascadeOnDelete();

            $table->foreign('room_id')
                ->references('room_id')
                ->on('room')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('course_session');
    }
};