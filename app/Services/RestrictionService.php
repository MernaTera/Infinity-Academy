<?php

namespace App\Services;

use App\Models\Enrollment\Enrollment;
use App\Models\Finance\InstallmentSchedule;
use Carbon\Carbon;

class RestrictionService
{
    /*
    |--------------------------------------------------------------------------
    | Check All Enrollments
    |--------------------------------------------------------------------------
    */

    public function checkAll()
    {
        $enrollments = Enrollment::with('installments')->get();

        foreach ($enrollments as $enrollment) {
            $this->evaluateEnrollment($enrollment);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Evaluate Single Enrollment
    |--------------------------------------------------------------------------
    */

    public function evaluateEnrollment($enrollment)
    {
        $overdue = $this->hasOverdueInstallments($enrollment);

        if ($overdue) {
            $this->applyRestriction($enrollment);
        } else {
            $this->removeRestriction($enrollment);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Check Overdue
    |--------------------------------------------------------------------------
    */

    private function hasOverdueInstallments($enrollment)
    {
        $graceDays = 3; // configurable later

        return $enrollment->installments()
            ->where('status', 'Pending')
            ->whereDate('due_date', '<', now()->subDays($graceDays))
            ->exists();
    }

    /*
    |--------------------------------------------------------------------------
    | Apply Restriction
    |--------------------------------------------------------------------------
    */

    private function applyRestriction($enrollment)
    {
        if (!$enrollment->restriction_flag) {
            $enrollment->update([
                'restriction_flag' => true
            ]);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Remove Restriction
    |--------------------------------------------------------------------------
    */

    private function removeRestriction($enrollment)
    {
        if ($enrollment->restriction_flag) {
            $enrollment->update([
                'restriction_flag' => false
            ]);
        }
    }
}
