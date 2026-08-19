<?php

namespace App\Models\Concerns;

use Illuminate\Database\Eloquent\Builder;
use App\Support\BranchContext;

/**
 * Adds an automatic "current branch" filter to any model that has a
 * `branch_id` column. Once a model uses this trait, EVERY query against it
 * (index listings, counts, relationship loads, etc.) is silently limited to
 * the branch of the currently logged-in staff member.
 *
 * Why a global scope instead of per-controller where() calls:
 *   Each branch is effectively a separate country/business. A single missed
 *   filter would leak another branch's students, money, or staff. A global
 *   scope makes the isolation impossible to forget — including in any feature
 *   written later.
 *
 * When the scope does NOT apply (see BranchContext::currentBranchId):
 *   - No authenticated user (console commands, queued jobs, the daily
 *     scheduler): these operate system-wide across every branch on purpose.
 *   - The logged-in user has no employee record / no branch: we do not want to
 *     silently hide everything for, e.g., a future student-portal login, so the
 *     scope simply stays off for them. Such users must be constrained by their
 *     own controllers, not by this branch filter.
 *
 * To deliberately bypass the filter (e.g. an owner-level cross-branch report),
 * call Model::withoutGlobalScope('branch') on that specific query.
 */
trait BelongsToBranch
{
    protected static function bootBelongsToBranch(): void
    {
        static::addGlobalScope('branch', function (Builder $query) {
            $branchId = BranchContext::currentBranchId();

            if ($branchId !== null) {
                // Qualify the column with the table name so the scope is safe
                // even when the query joins other tables that also have
                // a branch_id column.
                $table = $query->getModel()->getTable();
                $query->where($table . '.branch_id', $branchId);
            }
        });
    }

    /**
     * Convenience for the rare, explicit case where a query must see every
     * branch (reports, data migrations). Reads clearer than the raw
     * withoutGlobalScope('branch') call at the call site.
     */
    public function scopeAcrossAllBranches(Builder $query): Builder
    {
        return $query->withoutGlobalScope('branch');
    }
}