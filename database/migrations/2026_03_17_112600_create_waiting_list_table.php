<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('waiting_list', function (Blueprint $table) {
            $table->bigIncrements('waiting_id');

            $table->unsignedBigInteger('enrollment_id');
            $table->unsignedBigInteger('requested_patch_id')->nullable();

            $table->enum('preferred_type', ['Current_Patch','Next_Patch', 'Specific_Date']);
            $table->enum('preferred_delivery_mood', ['Online', 'Offline']);
            $table->enum('preferred_delivery_type' , ['Group', 'Private']);

            $table->date('preferred_start_date')->nullable();

            $table->enum('status', ['Active', 'Assigned', 'Cancelled'])
                  ->default('Active');
            
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('created_by_cs_id')->nullable();

            $table->timestamps();

            $table->unique('enrollment_id');

            $table->foreign('enrollment_id')
                  ->references('enrollment_id')
                  ->on('enrollment')
                  ->cascadeOnDelete();

            $table->foreign('requested_patch_id')
                  ->references('patch_id')
                  ->on('patch');

            $table->foreign('created_by_cs_id')
                  ->references('employee_id')
                  ->on('employee')
                  ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('waiting_list');
    }
};
