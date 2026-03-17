<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('refund_request', function (Blueprint $table) {
            $table->bigIncrements('request_id');

            $table->unsignedBigInteger('enrollment_id');
            $table->unsignedBigInteger('requested_by');

            $table->decimal('amount', 12, 2);

            $table->text('reason')->nullable();

            $table->enum('status', [
                'Pending',
                'Approved',
                'Rejected',
                'Processed'
            ])->default('Pending');

            $table->unsignedBigInteger('approved_by_admin_id')->nullable();

            $table->timestamp('approved_at')->nullable();

            $table->text('rejection_note')->nullable();

            $table->unsignedBigInteger('processed_transaction_id')->nullable();

            $table->timestamps();

            $table->foreign('enrollment_id')
                  ->references('enrollment_id')
                  ->on('enrollment')
                  ->cascadeOnDelete();

            $table->foreign('requested_by')
                  ->references('employee_id')
                  ->on('employee')
                  ->cascadeOnDelete();

            $table->foreign('approved_by_admin_id')
                  ->references('employee_id')
                  ->on('employee')
                  ->nullOnDelete();

            $table->foreign('processed_transaction_id')
                  ->references('transaction_id')
                  ->on('financial_transaction')
                  ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('refund_request');
    }
};
