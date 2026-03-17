<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('teacher', function (Blueprint $table) {
            $table->id('teacher_id');

            $table->unsignedBigInteger('employee_id')->unique();

            $table->unsignedBigInteger('english_level_id');

            $table->boolean('is_active')->default(true);

            $table->timestamps();

            $table->foreign('employee_id')
                ->references('employee_id')
                ->on('employee')
                ->cascadeOnDelete();

            $table->foreign('english_level_id')
                ->references('english_level_id')
                ->on('english_level')
                ->restrictOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('teacher');
    }
};
