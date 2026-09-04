<?php

namespace App\Support;

use App\Models\HR\Employee;
use Illuminate\Support\Facades\Auth;

class BranchContext
{
    protected static int|null|false $branchId = false;

    protected static ?int $override = null;

    public static function currentBranchId(): ?int
    {
        if (self::$override !== null) {
            return self::$override;
        }

        if (self::$branchId !== false) {
            return self::$branchId;
        }

        if (!Auth::check()) {
            return self::$branchId = null;
        }

        $branchId = Employee::withoutGlobalScopes()
            ->where('user_id', Auth::id())
            ->value('branch_id');

        return self::$branchId = ($branchId !== null ? (int) $branchId : null);
    }

    public static function hasBranch(): bool
    {
        return self::currentBranchId() !== null;
    }

    public static function setOverride(?int $branchId): void
    {
        self::$override = $branchId;
    }

    public static function forget(): void
    {
        self::$branchId = false;
        self::$override = null;
    }

    public static function adminEmployeeIdsForBranch(?int $branchId): array
    {
        if ($branchId === null) {
            return [];
        }

        return \App\Models\HR\Employee::withoutGlobalScopes()
            ->where('branch_id', $branchId)
            ->whereHas('user.role', fn($q) => $q->where('role_name', 'Admin'))
            ->pluck('employee_id')
            ->map(fn($id) => (int) $id)
            ->all();
    }
}