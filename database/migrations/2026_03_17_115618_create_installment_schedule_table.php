<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('installment_schedule', function (Blueprint $table) {
            $table->bigIncrements('installment_id');

            $table->unsignedBigInteger('enrollment_id');
            $table->unsignedBigInteger('transaction_id');

            $table->integer('installment_number');

            $table->date('due_date')->nullable();
            $table->integer('due_session_number')->nullable();

            $table->decimal('amount', 10, 2);

            $table->enum('status', ['Pending', 'Paid', 'Overdue'])->nullable();

            $table->timestamp('paid_at')->nullable();

            $table->timestamps();
            
            $table->unique(['enrollment_id', 'installment_number']);

            $table->index(['enrollment_id', 'status'], 'idx_installment_enrollment');
            $table->index('due_date', 'idx_installment_due_date');
            $table->index('status', 'idx_installment_status');

            $table->foreign('enrollment_id')
                  ->references('enrollment_id')
                  ->on('enrollment')
                  ->cascadeOnDelete();

            $table->foreign('transaction_id')
                  ->references('transaction_id')
                  ->on('financial_transaction')
                  ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('installment_schedule');
    }
};
