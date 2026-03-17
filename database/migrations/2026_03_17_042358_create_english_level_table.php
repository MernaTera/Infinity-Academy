<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('english_level', function (Blueprint $table) {
            $table->id('english_level_id');

            $table->string('level_name')->unique();

            $table->integer('level_rank')->unique();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('english_level');
    }
};
