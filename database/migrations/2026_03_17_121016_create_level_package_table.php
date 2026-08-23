<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('level_package', function (Blueprint $table) {
            $table->bigIncrements('package_id');

            $table->unsignedBigInteger('course_template_id');

            $table->string('name', 100)
                  ->comment('e.g. "3 Levels Package", "6 Levels Package"');

            $table->unsignedTinyInteger('levels_count')
                  ->comment('Number of levels included in this package');

            $table->decimal('package_price', 10, 2)
                  ->comment('Total price for all levels in the package');

            $table->boolean('is_active')->default(true);

            $table->unsignedBigInteger('created_by_admin_id')->nullable();

            $table->timestamps();

            $table->foreign('course_template_id')
                  ->references('course_template_id')
                  ->on('course_template')
                  ->cascadeOnDelete();

            $table->foreign('created_by_admin_id')
                  ->references('employee_id')
                  ->on('employee')
                  ->nullOnDelete();

            $table->index('course_template_id', 'idx_package_course');
        });

        // Add the enrollment.package_id foreign key here, now that BOTH tables
        // exist. It can't live in the add_package_tracking_to_enrollment
        // migration (2026_03_17_080211) because that runs before this table is
        // created. Guard for the column so it's safe on any DB state.
        if (Schema::hasTable('enrollment') && Schema::hasColumn('enrollment', 'package_id')) {
            Schema::table('enrollment', function (Blueprint $table) {
                $table->foreign('package_id')
                      ->references('package_id')
                      ->on('level_package')
                      ->nullOnDelete();
            });
        }
    }

    public function down(): void
    {
        // Drop the FK first (added in up()), then the table.
        if (Schema::hasTable('enrollment') && Schema::hasColumn('enrollment', 'package_id')) {
            Schema::table('enrollment', function (Blueprint $table) {
                $table->dropForeign(['package_id']);
            });
        }

        Schema::dropIfExists('level_package');
    }
};