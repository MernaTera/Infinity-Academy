<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('private_bundle', 'course_template_id')) {
            Schema::table('private_bundle', function (Blueprint $table) {
                $table->unsignedBigInteger('course_template_id')->nullable()->after('name');
                $table->foreign('course_template_id', 'private_bundle_course_fk')
                      ->references('course_template_id')
                      ->on('course_template')
                      ->nullOnDelete();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('private_bundle', 'course_template_id')) {
            Schema::table('private_bundle', function (Blueprint $table) {
                $table->dropForeign('private_bundle_course_fk');
                $table->dropColumn('course_template_id');
            });
        }
    }
};
