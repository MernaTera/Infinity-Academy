<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('material_assignment', function (Blueprint $table) {
            $table->id();

            $table->foreignId('material_id')
                ->constrained('materials', 'material_id')
                ->cascadeOnDelete();

            $table->foreignId('course_template_id')
                ->constrained('course_template', 'course_template_id')
                ->cascadeOnDelete();
            $table->foreignId('level_id')
                ->constrained('level', 'level_id')
                ->cascadeOnDelete();            
            $table->foreignId('sublevel_id')
                ->constrained('sublevel', 'sublevel_id')
                ->cascadeOnDelete();

            $table->boolean('is_mandatory')->default(false);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('material_assignment');
    }
};
