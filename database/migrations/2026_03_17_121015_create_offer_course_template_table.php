<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('offer_course_template', function (Blueprint $table) {
            $table->unsignedBigInteger('offer_id');
            $table->unsignedBigInteger('course_template_id');

            $table->primary(['offer_id', 'course_template_id']);

            $table->foreign('offer_id')
                  ->references('offer_id')
                  ->on('offer')
                  ->cascadeOnDelete();

            $table->foreign('course_template_id')
                  ->references('course_template_id')
                  ->on('course_template')
                  ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('offer_course_template');
    }
};