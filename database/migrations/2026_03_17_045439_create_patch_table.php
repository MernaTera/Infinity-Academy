<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('patch', function (Blueprint $table) {
            $table->id('patch_id');

            $table->string('name', 100);

            $table->unsignedBigInteger('branch_id');

            $table->date('start_date');
            $table->date('end_date');

            $table->enum('status', ['Upcoming','Active','Closed'])
                  ->default('Upcoming');

            $table->boolean('is_locked')->default(false);

            $table->boolean('is_placeholder')->default(false);

            $table->unsignedBigInteger('created_by_admin_id');

            $table->timestamps();

            $table->unique('name');

            $table->foreign('branch_id')
                ->references('branch_id')
                ->on('branch')
                ->cascadeOnDelete();

            $table->foreign('created_by_admin_id')
                ->references('employee_id')
                ->on('employee')
                ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('patch');
    }
};
