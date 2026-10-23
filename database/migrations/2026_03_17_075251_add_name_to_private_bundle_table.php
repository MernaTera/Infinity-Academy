<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('private_bundle', 'name')) {
            Schema::table('private_bundle', function (Blueprint $table) {
                $table->string('name', 100)->nullable()->after('bundle_id');
            });
        }

        DB::table('private_bundle')
            ->whereNull('name')
            ->update([
                'name' => DB::raw("CONCAT(TRIM(TRAILING '.' FROM TRIM(TRAILING '0' FROM hours)), ' Hours Bundle')"),
            ]);
    }

    public function down(): void
    {
        if (Schema::hasColumn('private_bundle', 'name')) {
            Schema::table('private_bundle', function (Blueprint $table) {
                $table->dropColumn('name');
            });
        }
    }
};
