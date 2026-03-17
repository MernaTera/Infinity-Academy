<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('payment_plan', function (Blueprint $table) {
            $table->bigIncrements('payment_plan_id');

            $table->string('name', 100);

            $table->decimal('deposit_percentage', 5, 2);
            $table->integer('installment_count');

            $table->integer('grace_period_days')->default(0);

            $table->boolean('requires_admin_approval')->default(false);
            $table->boolean('is_active')->default(true);

            $table->unsignedBigInteger('created_by_admin_id');

            $table->timestamp('created_at')->useCurrent();

            $table->foreign('created_by_admin_id')
                  ->references('employee_id')
                  ->on('employee')
                  ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_plan');
    }
};
