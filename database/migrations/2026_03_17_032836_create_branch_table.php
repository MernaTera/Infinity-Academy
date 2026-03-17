<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('branch', function (Blueprint $table) {
            $table->id('branch_id');

            $table->string('name', 150);
            $table->string('code', 50)->unique()->nullable();

            $table->string('address', 200)->nullable();
            $table->string('phone', 20)->nullable();

            $table->boolean('is_active')->default(true);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('branch');
    }
};
