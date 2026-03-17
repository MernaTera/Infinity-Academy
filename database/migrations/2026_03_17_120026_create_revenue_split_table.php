<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('revenue_split', function (Blueprint $table) {
            $table->bigIncrements('revenue_split_id');

            $table->unsignedBigInteger('transaction_id');
            $table->unsignedBigInteger('employee_id');
            $table->unsignedBigInteger('branch_id');
            $table->unsignedBigInteger('patch_id');

            $table->decimal('amount_allocated', 12, 2);

            $table->enum('allocation_type', [
                'Direct',
                'Shared',
                'Bonus'
            ]);

            $table->unsignedBigInteger('original_split_id')->nullable();

            $table->timestamps();

            $table->foreign('transaction_id')
                  ->references('transaction_id')
                  ->on('financial_transaction')
                  ->cascadeOnDelete();

            $table->foreign('employee_id')
                  ->references('employee_id')
                  ->on('employee')
                  ->cascadeOnDelete();

            $table->foreign('branch_id')
                  ->references('branch_id')
                  ->on('branch')
                  ->cascadeOnDelete();

            $table->foreign('patch_id')
                  ->references('patch_id')
                  ->on('patch')
                  ->cascadeOnDelete();

            $table->foreign('original_split_id')
                  ->references('revenue_split_id')
                  ->on('revenue_split')
                  ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('revenue_split');
    }
};
