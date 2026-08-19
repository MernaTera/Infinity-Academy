<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

/**
 * Makes the per-branch "settings" tables branch-scoped. Each branch is a
 * separate country with its own courses, prices, materials, schedules, etc.,
 * so these can no longer be shared globally.
 *
 * Every existing row is backfilled to branch 1 (the original branch that owns
 * all current data). After this runs, each *other* branch starts with none of
 * these and must have its own courses/plans/slots/etc. created -- until then a
 * CS in another branch will see empty pick-lists during registration. That is
 * expected and is the whole point of the isolation.
 *
 * english_level is deliberately NOT included: language levels (A1, A2, ...) are
 * universal and stay shared across all branches.
 */
return new class extends Migration
{
    private array $tables = [
        'materials'         => 'material_id',
        'course_template'   => 'course_template_id',
        'test_fee_settings' => 'id',
        'contract_type'     => 'contract_type_id',
        'payment_plan'      => 'payment_plan_id',
        'offer'             => 'offer_id',
        'level_package'     => 'package_id',
        'private_bundle'    => 'bundle_id',
        'time_slot'         => 'time_slot_id',
        'break_slot'        => 'break_slot_id',
    ];

    public function up(): void
    {
        foreach ($this->tables as $table => $afterColumn) {
            if (!Schema::hasColumn($table, 'branch_id')) {
                Schema::table($table, function (Blueprint $t) use ($afterColumn) {
                    $t->unsignedBigInteger('branch_id')->nullable()->after($afterColumn);
                    $t->index('branch_id');
                });

                DB::table($table)->whereNull('branch_id')->update(['branch_id' => 1]);
            }
        }
    }

    public function down(): void
    {
        foreach (array_keys($this->tables) as $table) {
            if (Schema::hasColumn($table, 'branch_id')) {
                Schema::table($table, function (Blueprint $t) {
                    $t->dropIndex(['branch_id']);
                    $t->dropColumn('branch_id');
                });
            }
        }
    }
};