<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('teacher_availability', function (Blueprint $table) {
            $table->id('availability_id');

            $table->unsignedBigInteger('teacher_id');
            $table->unsignedBigInteger('time_slot_id');

            $table->enum('day_of_week', [
                'Sun','Mon','Tue','Wed','Thu','Fri','Sat'
            ]);

            $table->timestamps();

            $table->foreign('teacher_id')
                ->references('teacher_id')
                ->on('teacher')
                ->cascadeOnDelete();

            $table->foreign('time_slot_id')
                ->references('time_slot_id')
                ->on('time_slot')
                ->cascadeOnDelete();

            $table->unique(['teacher_id', 'time_slot_id', 'day_of_week']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('teacher_availability');
    }
};
