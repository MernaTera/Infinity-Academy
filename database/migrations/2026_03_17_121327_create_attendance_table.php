<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('attendance', function (Blueprint $table) {
            $table->bigIncrements('attendance_id');

            $table->unsignedBigInteger('enrollment_id');
            $table->unsignedBigInteger('course_session_id');

            $table->enum('status', ['Present', 'Absent']);

            $table->unsignedBigInteger('recorded_by');

            $table->timestamp('recorded_at')->useCurrent();

            $table->unique(['enrollment_id', 'course_session_id']);

            $table->foreign('enrollment_id')
                  ->references('enrollment_id')
                  ->on('enrollment')
                  ->cascadeOnDelete();

            $table->foreign('course_session_id')
                  ->references('session_id')
                  ->on('course_session')
                  ->cascadeOnDelete();

            $table->foreign('recorded_by')
                  ->references('employee_id')
                  ->on('employee')
                  ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendance');
    }
};