<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Enrollment\Postponement;
use App\Services\AuditService;
use Carbon\Carbon;

class ExpirePostponements extends Command
{
    protected $signature   = 'postponements:expire';
    protected $description = 'Auto-expire postponements whose expected return date has passed (enrollment is forfeited, no refund).';

    public function handle(): void
    {
        $today = Carbon::today();

        $overdue = Postponement::with('enrollment')
            ->where('status', 'Active')
            ->whereDate('expected_return_date', '<', $today)
            ->get();

        $count = 0;

        foreach ($overdue as $postponement) {
            $postponement->update(['status' => 'Expired']);
            AuditService::updated(
                'postponement',
                $postponement->postponement_id,
                'status',
                'Active',
                'Expired'
            );

            $enrollment = $postponement->enrollment;
            if ($enrollment && $enrollment->status === 'Postponed') {
                $oldStatus = $enrollment->status;
                $enrollment->update(['status' => 'Expired']);
                AuditService::updated(
                    'enrollment',
                    $enrollment->enrollment_id,
                    'status',
                    $oldStatus,
                    'Expired'
                );
            }

            $count++;
            $this->info("Expired postponement #{$postponement->postponement_id} (enrollment #{$postponement->enrollment_id}).");
        }

        $this->info("Done. {$count} postponement(s) expired.");
    }
}
