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
        Schema::create('teacher_contract', function (Blueprint $table) {
            $table->id('contract_id');
            $table->unsignedBigInteger('teacher_id');
            $table->unsignedBigInteger('patch_id');
            $table->unsignedBigInteger('contract_type_id');
            $table->boolean('is_active')->default(true);
            $table->unsignedBigInteger('created_by_admin_id');
            $table->timestamps();

            $table->unique(['teacher_id', 'patch_id']);

            $table->foreign('teacher_id')->references('teacher_id')->on('teacher')->cascadeOnDelete();
            $table->foreign('patch_id')->references('patch_id')->on('patch')->cascadeOnDelete();
            $table->foreign('contract_type_id')->references('contract_type_id')->on('contract_type')->cascadeOnDelete();
            $table->foreign('created_by_admin_id')->references('employee_id')->on('employee')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('teacher_contract');
    }
};
