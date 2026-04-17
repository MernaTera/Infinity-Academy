<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('course_template', function (Blueprint $table) {
            $table->unsignedBigInteger('english_level_id')->nullable()->after('name');

            $table->foreign('english_level_id')
                ->references('english_level_id')
                ->on('english_level')
                ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('course_template', function (Blueprint $table) {
            //
        });
    }
};
