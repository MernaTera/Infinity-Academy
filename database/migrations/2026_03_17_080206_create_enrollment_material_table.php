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
        Schema::create('enrollment_material', function (Blueprint $table) {
            $table->id();

            $table->foreignId('enrollment_id')
                ->constrained('enrollment', 'enrollment_id')
                ->cascadeOnDelete();
            $table->foreignId('material_id')
                ->constrained('materials', 'material_id')
                ->cascadeOnDelete();

            $table->decimal('price', 10, 2);
            $table->enum('status', ['Pending', 'Paid'])->default('Pending');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('enrollment_material');
    }
};
