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
        Schema::table('lead', function (Blueprint $table) {
            $table->unsignedBigInteger('student_id')->nullable()->after('owner_cs_id');
            $table->foreign('student_id')
                ->references('student_id')
                ->on('student')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('lead', function (Blueprint $table) {
            $table->dropForeign(['student_id']);
            $table->dropColumn('student_id');
        });
    }
};
