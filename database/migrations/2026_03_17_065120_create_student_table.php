<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('student', function (Blueprint $table) {
            $table->id('student_id');

            $table->unsignedBigInteger('user_id')->nullable();

            $table->string('full_name')->nullable();

            $table->date('birthdate')->nullable();

            $table->string('degree')->nullable();

            $table->string('location')->nullable();

            $table->string('email')->unique()->nullable();

            $table->timestamps();

            $table->enum('status', [
                'Active','Archived','Dropped'
            ])->nullable();

            $table->boolean('is_active')->nullable();

            $table->index('user_id');
            $table->index('full_name');

            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->nullOnDelete()
                ->cascadeOnUpdate();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student');
    }
};