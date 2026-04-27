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
Schema::table('cs_target', function (Blueprint $table) {

    $table->dropForeign(['employee_id']);

    $table->dropUnique('cs_target_employee_id_patch_id_unique');

    $table->unsignedBigInteger('patch_id')->nullable()->change();

    $table->string('month', 7)->nullable()->after('patch_id');

    $table->foreign('employee_id')
          ->references('employee_id')
          ->on('employee')
          ->cascadeOnDelete();

    $table->unique(['employee_id', 'month']);
});
}

public function down(): void
{
    Schema::table('cs_target', function (Blueprint $table) {

        $table->dropForeign(['employee_id']);

        $table->dropUnique(['employee_id', 'month']);
        $table->dropColumn('month');

        $table->unsignedBigInteger('patch_id')->nullable(false)->change();

        $table->foreign('patch_id')
              ->references('patch_id')
              ->on('patch')
              ->cascadeOnDelete();

        $table->foreign('employee_id')
              ->references('employee_id')
              ->on('employee')
              ->cascadeOnDelete();

        $table->unique(['employee_id', 'patch_id']);
    });
}
};
