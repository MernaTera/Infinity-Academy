<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Makes the "no duplicates" rules on branch-scoped settings apply PER BRANCH
 * instead of across the whole system.
 *
 * Each branch is a separate country and must be able to define its own course
 * names, time slots, and break slots -- even when another branch already uses
 * the same name or the same 09:00-16:00 window. The original unique indexes
 * blocked that, raising a duplicate-key error the moment a second branch reused
 * a value. We drop each and re-add it with branch_id first so uniqueness is
 * scoped to the branch.
 *
 * Index drops are guarded (checked against information_schema) so the migration
 * is safe even if an index name differs or was already changed.
 */
return new class extends Migration
{
    private function dropIndexIfExists(string $table, string $index): void
    {
        $exists = DB::selectOne(
            "SELECT COUNT(*) AS c FROM information_schema.statistics
             WHERE table_schema = DATABASE() AND table_name = ? AND index_name = ?",
            [$table, $index]
        );

        if ($exists && (int) $exists->c > 0) {
            DB::statement("ALTER TABLE `{$table}` DROP INDEX `{$index}`");
        }
    }

    private function addUniqueIfMissing(string $table, string $index, array $columns): void
    {
        $exists = DB::selectOne(
            "SELECT COUNT(*) AS c FROM information_schema.statistics
             WHERE table_schema = DATABASE() AND table_name = ? AND index_name = ?",
            [$table, $index]
        );

        if (!$exists || (int) $exists->c === 0) {
            $cols = implode('`, `', $columns);
            DB::statement("ALTER TABLE `{$table}` ADD UNIQUE `{$index}` (`{$cols}`)");
        }
    }

    public function up(): void
    {
        // course_template: name unique -> (branch_id, name)
        $this->dropIndexIfExists('course_template', 'course_template_name_unique');
        $this->addUniqueIfMissing('course_template', 'course_template_branch_name_unique', ['branch_id', 'name']);

        // time_slot: (start_time, end_time) -> (branch_id, start_time, end_time)
        $this->dropIndexIfExists('time_slot', 'time_slot_start_time_end_time_unique');
        $this->addUniqueIfMissing('time_slot', 'time_slot_branch_start_end_unique', ['branch_id', 'start_time', 'end_time']);

        // break_slot: (start_time, end_time) -> (branch_id, start_time, end_time)
        $this->dropIndexIfExists('break_slot', 'break_slot_start_time_end_time_unique');
        $this->addUniqueIfMissing('break_slot', 'break_slot_branch_start_end_unique', ['branch_id', 'start_time', 'end_time']);
    }

    public function down(): void
    {
        $this->dropIndexIfExists('course_template', 'course_template_branch_name_unique');
        $this->addUniqueIfMissing('course_template', 'course_template_name_unique', ['name']);

        $this->dropIndexIfExists('time_slot', 'time_slot_branch_start_end_unique');
        $this->addUniqueIfMissing('time_slot', 'time_slot_start_time_end_time_unique', ['start_time', 'end_time']);

        $this->dropIndexIfExists('break_slot', 'break_slot_branch_start_end_unique');
        $this->addUniqueIfMissing('break_slot', 'break_slot_start_time_end_time_unique', ['start_time', 'end_time']);
    }
};