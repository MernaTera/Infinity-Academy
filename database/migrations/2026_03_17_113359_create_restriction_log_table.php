<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('restriction_log', function (Blueprint $table) {
            $table->bigIncrements('restriction_id');

            $table->unsignedBigInteger('enrollment_id');

            $table->enum('triggered_by', [
                'System',
                'Admin',
                'Customer_Service'
            ])->default('Customer_Service');

            $table->enum('reason', [
                'payment_queue',
                'absence_limit_exceeded',
                'installment_violation',
                'admin_manual'
            ]);

            $table->timestamp('triggered_at')->useCurrent();
            $table->timestamp('released_at')->nullable();

            $table->unsignedBigInteger('released_by')->nullable();


            $table->text('notes')->nullable();

            $table->foreign('enrollment_id')
                  ->references('enrollment_id')
                  ->on('enrollment')
                  ->cascadeOnDelete();

            $table->foreign('released_by')
                  ->references('employee_id')
                  ->on('employee')
                  ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('restriction_log');
    }
};
