<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Finance\InstallmentSchedule;
use App\Models\Enrollment\Enrollment;
use App\Models\Enrollment\RestrictionLog;

class ProcessOutstandingStatuses extends Command
{
    protected $signature   = 'outstanding:process'; 
    protected $description = 'Mark overdue installments and restrict enrollments';

    public function handle(): void
    {
        $updated = InstallmentSchedule::where('status', 'Pending')
            ->where('due_date', '<', today())
            ->update(['status' => 'Overdue']);

        $this->info("Marked Overdue: {$updated} installments.");

        $overdueEnrollmentIds = InstallmentSchedule::where('status', 'Overdue')
            ->pluck('enrollment_id')
            ->unique();

        $restricted = 0;
        foreach ($overdueEnrollmentIds as $enrollmentId) {
            $enrollment = Enrollment::find($enrollmentId);

            if (!$enrollment || $enrollment->status === 'Restricted') continue;
            if (!in_array($enrollment->status, ['Active', 'Waiting'])) continue;

            $enrollment->update([
                'status'             => 'Restricted',
                'restriction_flag'   => true,
                'restriction_reason' => 'OVERDUE_INSTALLMENT',
            ]);

            RestrictionLog::create([
                'enrollment_id' => $enrollmentId,
                'triggered_by'  => 'System',
                'reason'        => 'installment_violation', 
                'triggered_at'  => now(),
            ]);

            $restricted++;
        }

        $this->info("Restricted: {$restricted} enrollments.");
        $this->info('Done.');
    }
}