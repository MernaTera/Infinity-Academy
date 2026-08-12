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
                ->nullable() // ✅
                ->constrained('course_template', 'course_template_id')
                ->nullOnDelete(); // 🔥 مهم

            $table->foreignId('level_id')
                ->nullable() // ✅
                ->constrained('level', 'level_id')
                ->nullOnDelete();

            $table->foreignId('sublevel_id')
                ->nullable() // ✅
                ->constrained('sublevel', 'sublevel_id')
                ->nullOnDelete();

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
