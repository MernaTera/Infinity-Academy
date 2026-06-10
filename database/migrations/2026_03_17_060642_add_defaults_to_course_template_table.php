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
            $table->decimal('total_hours', 6, 2)->nullable()->after('price');
            $table->decimal('default_session_duration', 4, 2)->nullable()->after('total_hours');
            $table->integer('max_capacity')->nullable()->after('default_session_duration');
        });
    }

    public function down(): void
    {
        Schema::table('course_template', function (Blueprint $table) {
            $table->dropColumn(['total_hours', 'default_session_duration', 'max_capacity']);
        });
    }
};
