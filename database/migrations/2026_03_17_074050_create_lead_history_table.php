<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lead_history', function (Blueprint $table) {
            $table->id('history_id');

            $table->unsignedBigInteger('lead_id');

            $table->enum('old_status', [
                'Waiting',
                'Call_Again',
                'Scheduled_Call',
                'Registered',
                'Not_Interested',
                'Archived'
            ])->nullable();

            $table->enum('new_status', [
                'Waiting',
                'Call_Again',
                'Scheduled_Call',
                'Registered',
                'Not_Interested',
                'Archived'
            ])->nullable();

            $table->text('notes')->nullable();

            $table->unsignedBigInteger('changed_by');

            $table->timestamp('changed_at')->useCurrent();

            $table->index('lead_id');
            $table->index('changed_by');

            $table->foreign('lead_id')
                ->references('lead_id')
                ->on('lead')
                ->cascadeOnDelete();

            $table->foreign('changed_by')
                ->references('employee_id')
                ->on('employee')
                ->restrictOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lead_history');
    }
};