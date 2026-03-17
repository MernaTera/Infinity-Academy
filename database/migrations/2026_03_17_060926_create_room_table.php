<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('room', function (Blueprint $table) {
            $table->id('room_id');

            $table->string('name', 100);

            $table->unsignedBigInteger('branch_id');

            $table->integer('capacity');

            $table->enum('room_type', ['Offline','Online'])
                  ->default('Offline');

            $table->boolean('is_active')->default(true);

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
        Schema::dropIfExists('room');
    }
};
