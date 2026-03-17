<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('cs_target', function (Blueprint $table) {
            $table->bigIncrements('target_id');

            $table->unsignedBigInteger('employee_id');
            $table->unsignedBigInteger('patch_id');

            $table->decimal('target_amount', 12, 2)->nullable();
            $table->integer('target_registrations')->nullable();

            $table->boolean('is_locked')->default(false);

            $table->unsignedBigInteger('created_by_admin_id');

            $table->timestamps();

            $table->unique(['employee_id', 'patch_id']);

            $table->foreign('employee_id')
                  ->references('employee_id')
                  ->on('employee')
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
        Schema::dropIfExists('cs_target');
    }
};
