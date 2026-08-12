<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('enrollment', function (Blueprint $table) {
            $table->unsignedBigInteger('package_id')->nullable()->after('bundle_id');
            $table->integer('package_units_remaining')->nullable()->after('package_id');

            $table->foreign('package_id')
                  ->references('package_id')
                  ->on('level_package')
                  ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('enrollment', function (Blueprint $table) {
            $table->dropForeign(['package_id']);
            $table->dropColumn(['package_id', 'package_units_remaining']);
        });
    }
};