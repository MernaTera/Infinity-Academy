<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('placement_test', function (Blueprint $table) {
            $table->id('test_id');

            $table->unsignedBigInteger('student_id');

            $table->decimal('score', 5, 2);

            $table->unsignedBigInteger('assigned_level_id')->nullable();
            $table->unsignedBigInteger('override_level_id')->nullable();

            $table->decimal('test_fee', 10, 2);

            $table->boolean('fee_paid')->default(false);
            $table->boolean('deducted_from_course')->default(false);

            $table->unsignedBigInteger('created_by_cs_id')->nullable();

            $table->timestamps();

            $table->index('student_id');

            $table->foreign('student_id')
                ->references('student_id')
                ->on('student')
                ->cascadeOnDelete();

            $table->foreign('assigned_level_id')
                ->references('level_id')
                ->on('level')
                ->nullOnDelete();

            $table->foreign('override_level_id')
                ->references('level_id')
                ->on('level')
                ->nullOnDelete();

            $table->foreign('created_by_cs_id')
                ->references('employee_id')
                ->on('employee')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('placement_test');
    }
};