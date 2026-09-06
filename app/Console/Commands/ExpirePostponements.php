<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Enrollment\Postponement;
use App\Services\AuditService;
use Carbon\Carbon;

class ExpirePostponements extends Command
{
    protected $signature   = 'postponements:expire';
    protected $description = 'Auto-expire postponements whose expected return date has passed';

    public function handle(): void
    {
        $today = Carbon::today();

        $overdue = Postponement::with('enrollment')
            ->where('status', 'Active')
            ->whereDate('expected_return_date', '<', $today)
            ->get();

        foreach ($overdue as $p) {
            $p->update([
                'status'             => 'Expired',
                'actual_return_date' => null,
            ]);

            if ($p->enrollment) {
                $old = $p->enrollment->status;
                $p->enrollment->update(['status' => 'Expired']);
                AuditService::updated('enrollment', $p->enrollment->enrollment_id, 'status', $old, 'Expired');
            }

            $this->info("Expired postponement #{$p->postponement_id} (enrolment #{$p->enrollment_id}).");
        }

        $this->info('Done. Expired ' . $overdue->count() . ' postponement(s).');
    }
}
