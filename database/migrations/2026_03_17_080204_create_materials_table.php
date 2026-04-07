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
        if (!Schema::hasTable('materials')) {
            Schema::create('materials', function (Blueprint $table) {
                $table->id('material_id');
                $table->string('name');
                $table->decimal('price', 10, 2);
                $table->boolean('is_active')->default(true);
                $table->foreignId('created_by_admin_id')
                        ->nullable()
                        ->constrained('employee', 'employee_id')
                        ->nullOnDelete();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('materials');
    }
};
