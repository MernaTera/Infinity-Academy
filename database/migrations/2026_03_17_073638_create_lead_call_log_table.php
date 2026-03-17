<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lead_call_log', function (Blueprint $table) {
            $table->id('call_id');

            $table->unsignedBigInteger('lead_id');
            $table->unsignedBigInteger('cs_id');

            $table->timestamp('call_datetime')->useCurrent();

            $table->enum('outcome', [
                'No_Answer',
                'Interested',
                'Not_Interested',
                'Call_Again',
                'Registered',
                'Wrong_Number',
                'Follow_Up_Scheduled'
            ]);

            $table->text('notes')->nullable();

            $table->index('lead_id');
            $table->index('cs_id');
            $table->index('call_datetime');

            $table->foreign('lead_id')
                ->references('lead_id')
                ->on('lead')
                ->cascadeOnDelete();

            $table->foreign('cs_id')
                ->references('employee_id')
                ->on('employee')
                ->restrictOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lead_call_log');
    }
};