<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

/**
 * Gives `student` and `lead` their own branch_id so they can be branch-scoped
 * like every other core table. Neither had one before: a student's branch was
 * only implied by its enrollments, and a lead's by the CS who owns it.
 *
 * The column is added nullable, backfilled from those existing relationships,
 * and left nullable (a brand-new student/lead may legitimately have no branch
 * yet; the scope simply won't hide branchless rows from anyone until they get
 * a branch — and going forward the app should set branch_id at creation).
 */
return new class extends Migration
{
    public function up(): void
    {
        // Column-adds are guarded so a re-run after a mid-migration failure
        // (e.g. the earlier enrollment-backfill crash) doesn't error with
        // "column already exists".
        if (!Schema::hasColumn('student', 'branch_id')) {
            Schema::table('student', function (Blueprint $table) {
                $table->unsignedBigInteger('branch_id')->nullable()->after('user_id');
                $table->index('branch_id');
            });
        }

        if (!Schema::hasColumn('lead', 'branch_id')) {
            Schema::table('lead', function (Blueprint $table) {
                $table->unsignedBigInteger('branch_id')->nullable()->after('owner_cs_id');
                $table->index('branch_id');
            });
        }

        // ── Backfill students from their enrollments ──────────────────
        // A student belongs to the branch of their enrollment(s). We take the
        // branch from any one enrollment (they should all share a branch).
        //
        // Guarded by a table-existence check: on a fresh install this migration
        // runs BEFORE the enrollment table is created (its timestamp is later),
        // so there is nothing to backfill yet and the query would otherwise
        // crash with "Table 'enrollment' doesn't exist". New students get their
        // branch_id at creation time; only pre-existing data needs backfilling.
        if (Schema::hasTable('enrollment')) {
            DB::statement("
                UPDATE student s
                JOIN (
                    SELECT student_id, MIN(branch_id) AS branch_id
                    FROM enrollment
                    WHERE branch_id IS NOT NULL
                    GROUP BY student_id
                ) e ON e.student_id = s.student_id
                SET s.branch_id = e.branch_id
            ");
        }

        // ── Backfill leads from the CS who owns them ──────────────────
        // A lead's branch is the branch of its owning CS employee. The employee
        // table already exists by this point, but guard it too for safety.
        if (Schema::hasTable('employee')) {
            DB::statement("
                UPDATE lead l
                JOIN employee emp ON emp.employee_id = l.owner_cs_id
                SET l.branch_id = emp.branch_id
                WHERE l.owner_cs_id IS NOT NULL
                  AND emp.branch_id IS NOT NULL
            ");
        }
    }

    public function down(): void
    {
        Schema::table('student', function (Blueprint $table) {
            $table->dropIndex(['branch_id']);
            $table->dropColumn('branch_id');
        });

        Schema::table('lead', function (Blueprint $table) {
            $table->dropIndex(['branch_id']);
            $table->dropColumn('branch_id');
        });
    }
};