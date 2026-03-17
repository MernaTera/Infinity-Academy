<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('private_bundle', function (Blueprint $table) {
            $table->id('bundle_id');

            $table->decimal('hours', 6, 2);
            $table->decimal('price', 10, 2);

            $table->boolean('is_active')->default(true);

            $table->unsignedBigInteger('created_by_admin_id')->nullable();

            $table->timestamps();

            $table->foreign('created_by_admin_id')
                ->references('employee_id')
                ->on('employee')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('private_bundle');
    }
};