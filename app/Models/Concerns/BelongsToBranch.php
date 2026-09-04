<?php

namespace App\Models\Concerns;

use Illuminate\Database\Eloquent\Builder;
use App\Support\BranchContext;

trait BelongsToBranch
{
    protected static function bootBelongsToBranch(): void
    {
        static::addGlobalScope('branch', function (Builder $query) {
            $branchId = BranchContext::currentBranchId();

            if ($branchId !== null) {
                $table = $query->getModel()->getTable();
                $query->where($table . '.branch_id', $branchId);
            }
        });

        static::creating(function ($model) {
            if ($model->branch_id === null) {
                $branchId = BranchContext::currentBranchId();
                if ($branchId !== null) {
                    $model->branch_id = $branchId;
                }
            }
        });
    }

    public function scopeAcrossAllBranches(Builder $query): Builder
    {
        return $query->withoutGlobalScope('branch');
    }
}