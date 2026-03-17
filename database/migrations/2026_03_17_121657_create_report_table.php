<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('report', function (Blueprint $table) {
            $table->bigIncrements('report_id');

            $table->unsignedBigInteger('enrollment_id');
            $table->unsignedBigInteger('teacher_id');

            $table->decimal('total_score', 5, 2)->default(0);

            $table->enum('status', [
                'Draft',
                'Submitted',
                'Approved',
                'Rejected',
                'Sent'
            ])->default('Draft');

            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('approved_at')->nullable();

            $table->unsignedBigInteger('approved_by_admin_id')->nullable();

            $table->timestamp('sent_at')->nullable();

            $table->text('rejection_note')->nullable();

            $table->boolean('pdf_generated')->default(false);

            $table->timestamps();

            $table->unique('enrollment_id', 'unique_report_per_enrollment');

            $table->foreign('enrollment_id')
                  ->references('enrollment_id')
                  ->on('enrollment')
                  ->cascadeOnDelete();

            $table->foreign('teacher_id')
                  ->references('teacher_id')
                  ->on('teacher')
                  ->cascadeOnDelete();

            $table->foreign('approved_by_admin_id')
                  ->references('employee_id')
                  ->on('employee')
                  ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('report');
    }
};
