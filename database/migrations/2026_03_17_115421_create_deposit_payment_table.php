<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('deposit_payment', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->unsignedBigInteger('enrollment_id');

            $table->enum('method', [
                'Cash',
                'Instapay',
                'Vodafone_Cash',
            ]);

            $table->decimal('amount', 10, 2);

            $table->string('reference_number')->nullable()
                  ->comment('For Instapay/Vodafone Cash transaction reference');

            $table->timestamps();

            $table->foreign('enrollment_id')
                  ->references('enrollment_id')
                  ->on('enrollment')
                  ->cascadeOnDelete();

            $table->index('enrollment_id', 'idx_deposit_enrollment');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('deposit_payment');
    }
};