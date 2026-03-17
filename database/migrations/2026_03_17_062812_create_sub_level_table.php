<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sublevel', function (Blueprint $table) {
            $table->id('sublevel_id');

            $table->unsignedBigInteger('level_id');

            $table->string('name');

            $table->unsignedBigInteger('sublevel_order');

            $table->decimal('total_hours', 6, 2);

            $table->decimal('default_session_duration', 4, 2)->nullable();

            $table->integer('max_capacity');

            $table->unsignedBigInteger('teacher_min_level')->nullable();

            $table->decimal('price', 10, 2)->nullable();

            $table->boolean('is_active')->default(true);

            $table->unsignedBigInteger('created_by_admin_id')->nullable();

            $table->timestamps();

            $table->index('level_id');

            $table->foreign('level_id')
                ->references('level_id')
                ->on('level')
                ->cascadeOnDelete();

            $table->foreign('teacher_min_level')
                ->references('english_level_id')
                ->on('english_level')
                ->nullOnDelete();

            $table->foreign('created_by_admin_id')
                ->references('employee_id')
                ->on('employee')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sublevel');
    }
};