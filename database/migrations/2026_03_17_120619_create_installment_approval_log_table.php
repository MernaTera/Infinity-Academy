<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('installment_approval_log', function (Blueprint $table) {
            $table->bigIncrements('approval_id');

            $table->unsignedBigInteger('enrollment_id');
            $table->unsignedBigInteger('payment_plan_id');
            $table->unsignedBigInteger('request_by_cs_id');

            $table->enum('status', [
                'Pending',
                'Approved',
                'Rejected'
            ])->default('Pending');

            $table->unsignedBigInteger('approved_by_admin_id')->nullable();

            $table->timestamp('approved_at')->nullable();

            $table->text('rejection_note')->nullable();

            $table->timestamps();

            $table->foreign('enrollment_id')
                  ->references('enrollment_id')
                  ->on('enrollment')
                  ->cascadeOnDelete();

            $table->foreign('payment_plan_id')
                  ->references('payment_plan_id')
                  ->on('payment_plan')
                  ->cascadeOnDelete();

            $table->foreign('request_by_cs_id')
                  ->references('employee_id')
                  ->on('employee')
                  ->cascadeOnDelete();

            $table->foreign('approved_by_admin_id')
                  ->references('employee_id')
                  ->on('employee')
                  ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('installment_approval_log');
    }
};
