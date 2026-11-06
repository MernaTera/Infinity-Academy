<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('enrollment', function (Blueprint $table) {
            $table->enum('package_progression', ['Next_Level', 'Repeat'])->nullable()->after('package_units_remaining');
            $table->unsignedBigInteger('previous_enrollment_id')->nullable()->after('package_progression');

            $table->foreign('previous_enrollment_id', 'enrollment_previous_enrollment_fk')
                ->references('enrollment_id')
                ->on('enrollment')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('enrollment', function (Blueprint $table) {
            $table->dropForeign('enrollment_previous_enrollment_fk');
            $table->dropColumn(['package_progression', 'previous_enrollment_id']);
        });
    }
};
