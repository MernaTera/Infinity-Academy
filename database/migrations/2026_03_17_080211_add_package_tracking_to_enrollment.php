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
        });

        // NOTE: the package_id → level_package foreign key is intentionally NOT
        // added here. This migration's timestamp runs BEFORE the level_package
        // table is created (2026_03_17_121016), so adding the FK now fails with
        // "Foreign key constraint is incorrectly formed". The FK is added at the
        // end of the create_level_package migration, once both tables exist.
    }

    public function down(): void
    {
        Schema::table('enrollment', function (Blueprint $table) {
            $table->dropColumn(['package_id', 'package_units_remaining']);
        });
    }
};