<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('financial_transaction', function (Blueprint $table) {
            $table->bigIncrements('transaction_id');

            // Foreign Keys
            $table->unsignedBigInteger('enrollment_id');
            $table->unsignedBigInteger('patch_id');
            $table->unsignedBigInteger('branch_id');
            $table->unsignedBigInteger('created_by_employee_id')->nullable();

            // Enums
            $table->enum('transaction_type', [
                'Payment',
                'Installment',
                'Refund',
                'Material',
                'Test_Fee',
                'Adjustment'
            ]);

            $table->enum('transaction_category', [
                'Course',
                'Material',
                'Test',
                'Other'
            ]);

            $table->decimal('amount', 12, 2);

            $table->enum('payment_method', [
                'Cash',
                'Card',
                'Transfer',
                'Online'
            ])->default('Cash');

            $table->string('reference_number')->nullable();
            $table->text('notes')->nullable();

            $table->timestamps();

            $table->index('created_at', 'idx_financial_created');

            $table->foreign('enrollment_id')
                ->references('enrollment_id')
                ->on('enrollment')
                ->cascadeOnDelete();

            $table->foreign('patch_id')
                ->references('patch_id')
                ->on('patch');

            $table->foreign('branch_id')
                ->references('branch_id')
                ->on('branch');

            $table->foreign('created_by_employee_id')
                ->references('employee_id')
                ->on('employee')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('financial_transaction');
    }
};
