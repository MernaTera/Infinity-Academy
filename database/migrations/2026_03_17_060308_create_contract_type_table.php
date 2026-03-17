<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contract_type', function (Blueprint $table) {
            $table->id('contract_id');

            $table->unsignedBigInteger('teacher_id');
            $table->unsignedBigInteger('patch_id');

            $table->enum('contract_type', ['PT','FT','OT']);

            $table->integer('max_sessions_allowed');

            $table->boolean('is_active')->default(true);

            $table->unsignedBigInteger('created_by_admin_id');

            $table->timestamps();

            $table->unique(['teacher_id', 'patch_id']);

            $table->foreign('teacher_id')
                ->references('teacher_id')
                ->on('teacher')
                ->cascadeOnDelete();

            $table->foreign('patch_id')
                ->references('patch_id')
                ->on('patch')
                ->cascadeOnDelete();

            $table->foreign('created_by_admin_id')
                ->references('employee_id')
                ->on('employee')
                ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contract_type');
    }
};