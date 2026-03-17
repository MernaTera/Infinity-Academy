<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('bundle_usage_log', function (Blueprint $table) {
            $table->bigIncrements('usage_id');

            $table->unsignedBigInteger('enrollment_id');
            $table->unsignedBigInteger('course_session_id');

            $table->decimal('hours_deducted', 6, 2);

            $table->enum('reason', [
                'ATTENDANCE',
                'MANUAL_ADJUSTMENT'
            ])->nullable();

            $table->unsignedBigInteger('created_by_cs_id')->nullable();

            $table->timestamps(); 

            $table->foreign('enrollment_id')
                  ->references('enrollment_id')
                  ->on('enrollment')
                  ->cascadeOnDelete();

            $table->foreign('course_session_id')
                  ->references('session_id')
                  ->on('course_session')
                  ->cascadeOnDelete();

            $table->foreign('created_by_cs_id')
                  ->references('employee_id')
                  ->on('employee')
                  ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bundle_usage_log');
    }
};
