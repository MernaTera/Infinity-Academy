<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lead', function (Blueprint $table) {
            $table->id('lead_id');

            $table->string('full_name')->nullable();

            $table->string('phone', 50)->unique()->nullable();

            $table->date('birthdate')->nullable();

            $table->string('location')->nullable();

            $table->enum('source', [
                'Facebook','Website','Friend','Walk_In','Google','Other'
            ]);

            $table->enum('degree', ['Student','Graduate']);

            $table->unsignedBigInteger('interested_course_template_id')->nullable();
            $table->unsignedBigInteger('interested_level_id')->nullable();
            $table->unsignedBigInteger('interested_sublevel_id')->nullable();

            $table->enum('status', [
                'Waiting',
                'Call_Again',
                'Scheduled_Call',
                'Registered',
                'Not_Interested',
                'Archived'
            ])->default('Waiting');

            $table->enum('start_preference_type', [
                'Current Patch',
                'Next Patch',
                'Specific Date'
            ])->nullable();

            $table->dateTime('next_call_at')->nullable();

            $table->unsignedBigInteger('owner_cs_id')->nullable();

            $table->boolean('is_active')->default(true);

            $table->text('notes')->nullable();

            $table->timestamps();

            $table->index('owner_cs_id');

            $table->foreign('owner_cs_id')
                ->references('employee_id')
                ->on('employee')
                ->nullOnDelete();

            $table->foreign('interested_course_template_id')
                ->references('course_template_id')
                ->on('course_template')
                ->nullOnDelete();

            $table->foreign('interested_level_id')
                ->references('level_id')
                ->on('level')
                ->nullOnDelete();

            $table->foreign('interested_sublevel_id')
                ->references('sublevel_id')
                ->on('sublevel')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lead');
    }
};