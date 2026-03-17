<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('report_score', function (Blueprint $table) {
            $table->bigIncrements('score_id');

            $table->unsignedBigInteger('report_id');

            $table->string('component_name', 100);

            $table->decimal('max_score', 5, 2);
            $table->decimal('student_score', 5, 2);

            $table->timestamps();
            
            $table->foreign('report_id')
                  ->references('report_id')
                  ->on('report')
                  ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('report_score');
    }
};
