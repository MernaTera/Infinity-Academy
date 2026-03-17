<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('employee', function (Blueprint $table) {
            $table->id('employee_id');

            $table->string('full_name');

            // FK → users
            $table->unsignedBigInteger('user_id');

            // FK → branch
            $table->unsignedBigInteger('branch_id');

            $table->decimal('salary', 12, 2)->nullable();

            $table->enum('status', ['Active', 'Inactive'])
                  ->default('Active');

            $table->timestamp('hired_at');

            $table->timestamps();


            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->cascadeOnDelete();

            $table->foreign('branch_id')
                ->references('branch_id')
                ->on('branch')
                ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employee');
    }
};
