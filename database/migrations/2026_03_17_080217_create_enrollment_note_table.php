<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('enrollment_note', function (Blueprint $table) {
            $table->bigIncrements('note_id');

            $table->unsignedBigInteger('enrollment_id');
            $table->text('body');
            $table->unsignedBigInteger('created_by_cs_id')->nullable();

            $table->timestamps();

            $table->foreign('enrollment_id')
                  ->references('enrollment_id')
                  ->on('enrollment')
                  ->cascadeOnDelete();

            $table->foreign('created_by_cs_id')
                  ->references('employee_id')
                  ->on('employee')
                  ->nullOnDelete();

            $table->index('enrollment_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('enrollment_note');
    }
};
