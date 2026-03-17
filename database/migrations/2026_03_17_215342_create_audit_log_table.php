<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('audit_log', function (Blueprint $table) {
            $table->bigIncrements('audit_log_id');

            $table->string('table_name', 100);

            $table->unsignedBigInteger('record_id');

            $table->string('field_name', 100);

            $table->enum('action_type', ['Create', 'Update', 'Delete']);

            $table->text('old_value')->nullable();
            $table->text('new_value')->nullable();

            $table->unsignedBigInteger('changed_by');

            $table->timestamps();

            $table->foreign('changed_by')
                  ->references('employee_id')
                  ->on('employee')
                  ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('audit_log');
    }
};
