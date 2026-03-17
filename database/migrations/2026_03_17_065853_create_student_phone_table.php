<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('student_phone', function (Blueprint $table) {
            $table->id('phone_id');

            $table->unsignedBigInteger('student_id');

            $table->string('phone_number', 20);

            $table->boolean('is_primary')->default(false);

            $table->timestamps();

            $table->index('student_id');

            $table->unique('phone_number', 'uq_phone_number');

            $table->foreign('student_id')
                ->references('student_id')
                ->on('student')
                ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_phone');
    }
};