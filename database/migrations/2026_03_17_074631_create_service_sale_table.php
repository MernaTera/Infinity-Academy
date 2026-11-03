<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('service_sale', function (Blueprint $table) {
            $table->bigIncrements('service_sale_id');
            $table->unsignedBigInteger('branch_id');
            $table->unsignedBigInteger('patch_id')->nullable();
            $table->unsignedBigInteger('lead_id')->nullable();
            $table->unsignedBigInteger('student_id')->nullable();
            $table->string('client_name');
            $table->string('client_phone')->nullable();
            $table->enum('service_type', ['Test', 'Certificate']);
            $table->unsignedBigInteger('test_fee_setting_id')->nullable();
            $table->unsignedBigInteger('certificate_setting_id')->nullable();
            $table->string('item_name');
            $table->decimal('price', 10, 2);
            $table->enum('payment_method', ['Cash', 'Card', 'Transfer', 'Online'])->default('Cash');
            $table->string('reference_number')->nullable();
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('created_by_cs_id')->nullable();
            $table->timestamps();

            $table->foreign('branch_id')->references('branch_id')->on('branch');
            $table->foreign('patch_id')->references('patch_id')->on('patch')->nullOnDelete();
            $table->foreign('lead_id')->references('lead_id')->on('lead')->nullOnDelete();
            $table->foreign('student_id')->references('student_id')->on('student')->nullOnDelete();
            $table->foreign('test_fee_setting_id')->references('id')->on('test_fee_settings')->nullOnDelete();
            $table->foreign('certificate_setting_id')->references('id')->on('certificate_settings')->nullOnDelete();
            $table->foreign('created_by_cs_id')->references('employee_id')->on('employee')->nullOnDelete();

            $table->index(['branch_id', 'created_at'], 'idx_service_sale_branch_created');
            $table->index(['created_by_cs_id', 'created_at'], 'idx_service_sale_cs_created');
            $table->index('service_type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('service_sale');
    }
};
