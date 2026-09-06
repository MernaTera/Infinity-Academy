<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('enrollment_note', function (Blueprint $table) {
            $table->bigIncrements('note_id');

            $table->unsignedBigInteger('enrollment_id');
            $table->unsignedBigInteger('created_by_employee_id')->nullable();

            $table->text('note');

            $table->timestamps();

            $table->foreign('enrollment_id')
                  ->references('enrollment_id')->on('enrollment')
                  ->cascadeOnDelete();

            $table->foreign('created_by_employee_id')
                  ->references('employee_id')->on('employee')
                  ->nullOnDelete();

            $table->index(['enrollment_id', 'note_id'], 'idx_enrollment_note');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('enrollment_note');
    }
};
