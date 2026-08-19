<?php

namespace App\Support;

use App\Models\HR\Employee;
use Illuminate\Support\Facades\Auth;

/**
 * Single source of truth for "which branch is the current request scoped to".
 *
 * Every branch is a separate country/business, so almost every query in the
 * system must be limited to the staff member's branch. Resolving that branch
 * touches the database (user -> employee -> branch_id); because the global
 * BelongsToBranch scope runs on *every* query, we resolve it once per request
 * and cache it here to avoid an extra lookup on each query (an N+1 that would
 * otherwise be very real).
 */
class BranchContext
{
    /**
     * Cached resolution for the current request.
     *   - unset (property is false): not resolved yet
     *   - null: resolved, but there is no branch (no auth / no employee)
     *   - int: the resolved branch id
     */
    protected static int|null|false $branchId = false;

    /**
     * A branch id explicitly forced for the current request (used by tests or
     * any future "act as branch" tooling). Takes precedence over the logged-in
     * user's own branch.
     */
    protected static ?int $override = null;

    /**
     * The branch the current request should be limited to, or null when the
     * request is not branch-scoped (console/jobs, or a user with no employee).
     */
    public static function currentBranchId(): ?int
    {
        if (self::$override !== null) {
            return self::$override;
        }

        if (self::$branchId !== false) {
            return self::$branchId;
        }

        // No authenticated user -> system-wide (console commands, queued jobs,
        // the daily scheduler). These are meant to run across all branches.
        if (!Auth::check()) {
            return self::$branchId = null;
        }

        // Resolve via the staff member's employee record. Students/teachers who
        // are not employees simply have no branch here; their own controllers
        // must constrain them (the branch filter stays off rather than hiding
        // everything from them).
        // Resolve via the staff member's employee record. We strip any global
        // scopes here (notably the branch scope, once Employee itself uses
        // BelongsToBranch) so this lookup never re-enters the branch scope,
        // which would recurse infinitely: the scope needs the branch, and the
        // branch is resolved by this very query.
        $branchId = Employee::withoutGlobalScopes()
            ->where('user_id', Auth::id())
            ->value('branch_id');

        return self::$branchId = ($branchId !== null ? (int) $branchId : null);
    }

    /** True when the current request is limited to a single branch. */
    public static function hasBranch(): bool
    {
        return self::currentBranchId() !== null;
    }

    /**
     * Force a branch for the current request. Mainly for tests / future
     * "view as branch" tooling. Pass null to clear the override.
     */
    public static function setOverride(?int $branchId): void
    {
        self::$override = $branchId;
    }

    /**
     * Drop the cached value so the next call re-resolves. Useful after login
     * within the same request lifecycle, or between tests.
     */
    public static function forget(): void
    {
        self::$branchId = false;
        self::$override = null;
    }

    /**
     * The employee IDs of all admins in a given branch. Used when notifying
     * admins about branch events (installment approvals, refunds, contract
     * overages, teacher reports) so the ping only reaches the branch that owns
     * the event — never admins in other, isolated branches.
     *
     * Returns a plain array of ints, which suits both Eloquent loops and raw
     * query-builder inserts. When $branchId is null (unknown branch) it returns
     * an empty array rather than every admin, since a branchless notification
     * should not fan out across all branches.
     */
    public static function adminEmployeeIdsForBranch(?int $branchId): array
    {
        if ($branchId === null) {
            return [];
        }

        return \App\Models\HR\Employee::query()
            ->where('branch_id', $branchId)
            ->whereHas('user.role', fn($q) => $q->where('role_name', 'Admin'))
            ->pluck('employee_id')
            ->map(fn($id) => (int) $id)
            ->all();
    }
}