<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('user_notification', function (Blueprint $table) {
            $table->bigIncrements('user_notification_id');

            $table->unsignedBigInteger('employee_id');

            $table->string('title', 150);
            $table->text('message');

            $table->string('related_entity_type', 50)->nullable();
            $table->unsignedBigInteger('related_entity_id')->nullable();

            $table->boolean('is_read')->default(false);

            $table->timestamps();

            $table->foreign('employee_id')
                  ->references('employee_id')
                  ->on('employee')
                  ->cascadeOnDelete();

            $table->index(['employee_id', 'is_read'], 'idx_employee_read');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_notification');
    }
};