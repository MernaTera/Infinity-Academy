<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('level', function (Blueprint $table) {
            $table->id('level_id');

            $table->unsignedBigInteger('course_template_id');

            $table->string('name');

            $table->decimal('price', 10, 2);

            $table->integer('level_order');

            $table->decimal('total_hours', 6, 2);

            $table->decimal('default_session_duration', 4, 2);

            $table->integer('max_capacity');

            $table->unsignedBigInteger('teacher_level');

            $table->boolean('is_active')->default(true);

            $table->unsignedBigInteger('created_by_admin_id')->nullable();

            $table->timestamps();

            $table->index('course_template_id');
            $table->index('teacher_level');

            $table->foreign('course_template_id')
                ->references('course_template_id')
                ->on('course_template')
                ->cascadeOnDelete();

            $table->foreign('teacher_level')
                ->references('english_level_id')
                ->on('english_level')
                ->restrictOnDelete();

            $table->foreign('created_by_admin_id')
                ->references('employee_id')
                ->on('employee')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('level');
    }
};