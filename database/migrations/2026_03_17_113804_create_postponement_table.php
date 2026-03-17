<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('postponement', function (Blueprint $table) {
            $table->bigIncrements('postponement_id');

            $table->unsignedBigInteger('enrollment_id');

            $table->date('start_date');
            $table->date('expected_return_date');
            $table->date('actual_return_date')->nullable();

            $table->enum('status', ['Active', 'Returned', 'Expired'])
                  ->default('Active');

            $table->text('reason')->nullable();

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

        });
    }

    public function down(): void
    {
        Schema::dropIfExists('postponement');
    }
};
