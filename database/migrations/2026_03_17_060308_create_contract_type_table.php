<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contract_type', function (Blueprint $table) {
            $table->id('contract_type_id');           // ✅ PK جديد
            $table->string('name');                    // ✅ اسم حر
            $table->integer('max_sessions_allowed');
            $table->boolean('is_active')->default(true);
            $table->unsignedBigInteger('created_by_admin_id');
            $table->timestamps();

            $table->foreign('created_by_admin_id')
                ->references('employee_id')
                ->on('employee')
                ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contract_type');
    }
};