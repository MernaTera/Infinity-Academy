<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('enrollment', function (Blueprint $table) {
            $table->bigIncrements('enrollment_id');

            // FK columns
            $table->unsignedBigInteger('student_id');
            $table->unsignedBigInteger('placement_test_id')->nullable();

            $table->unsignedBigInteger('level_id')->nullable();
            $table->unsignedBigInteger('sublevel_id')->nullable();

            $table->unsignedBigInteger('course_instance_id');
            $table->unsignedBigInteger('patch_id');
            $table->unsignedBigInteger('branch_id')->nullable();

            // Enums
            $table->enum('enrollment_type', ['Group', 'Private'])->nullable();
            $table->enum('delivery_mood', ['Online', 'Offline'])->nullable();

            // Dates
            $table->date('preference_start_date')->nullable();
            $table->date('actual_start_date')->nullable();

            // Numbers
            $table->decimal('hours_remaining', 6, 2)->nullable();
            $table->decimal('final_price', 10, 2)->nullable();

            $table->unsignedBigInteger('payment_plan_id');
            $table->unsignedBigInteger('bundle_id')->nullable();

            $table->decimal('discount_value', 10, 2)->nullable();

            $table->enum('status', [
                'Pending_Approval',
                'Active',
                'Restricted',
                'Completed',
                'Postponed',
                'Cancelled',
                'Expired'
            ])->nullable();

            $table->boolean('restriction_flag')->default(false);

            $table->unsignedBigInteger('created_by_cs_id')->nullable();

            $table->timestamps();

            $table->index('student_id', 'idx_enrollment_student');
            $table->index('course_instance_id', 'idx_enrollment_instance');

            $table->unique(['student_id', 'course_instance_id'], 'unique_student_instance');

            $table->foreign('student_id')
                  ->references('student_id')
                  ->on('student')
                  ->cascadeOnDelete();

            $table->foreign('placement_test_id')
                  ->references('test_id')
                  ->on('placement_test')
                  ->nullOnDelete();

            $table->foreign('course_instance_id')
                  ->references('course_instance_id')
                  ->on('course_instance')
                  ->cascadeOnDelete();

            $table->foreign('patch_id')
                  ->references('patch_id')
                  ->on('patch')
                  ->cascadeOnDelete();

            $table->foreign('level_id')
                  ->references('level_id')
                  ->on('level')
                  ->nullOnDelete();

            $table->foreign('sublevel_id')
                  ->references('sublevel_id')
                  ->on('sublevel')
                  ->nullOnDelete();

            $table->foreign('bundle_id')
                  ->references('bundle_id')
                  ->on('private_bundle')
                  ->nullOnDelete();

            $table->foreign('created_by_cs_id')
                  ->references('employee_id')
                  ->on('employee')
                  ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('enrollment');
    }
};
