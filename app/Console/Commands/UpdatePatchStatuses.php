<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Academic\Patch;
use App\Services\AuditService;
use Carbon\Carbon;

class UpdatePatchStatuses extends Command
{
    protected $signature   = 'patches:update-statuses';
    protected $description = 'Auto-close expired patches and auto-activate patches that have started';

    public function handle(): void
    {
        $today = Carbon::today();

        $expired = Patch::where('status', 'Active')
            ->where('end_date', '<', $today)
            ->get();

        foreach ($expired as $patch) {
            $patch->update(['status' => 'Closed', 'is_locked' => true]);
            AuditService::updated('patch', $patch->patch_id, 'status', 'Active', 'Closed');

            \App\Models\Enrollment\Enrollment::where('patch_id', $patch->patch_id)
                ->where('status', 'Active')
                ->where(function ($q) {
                    $q->where(function ($p) {
                        $p->where('enrollment_type', 'Private')
                          ->where('hours_remaining', '>', 0);
                    })->orWhere(function ($p) {
                        $p->whereNotNull('package_id')
                          ->where('package_units_remaining', '>', 0);
                    })->orWhere(function ($p) {
                        $p->whereNull('package_id')
                          ->where('enrollment_type', '!=', 'Private');
                    });
                })
                ->update(['status' => 'Completed']);

            $this->info("Closed: {$patch->name}");
        }

        $started = Patch::where('status', 'Upcoming')
            ->where('start_date', '<=', $today)
            ->get();

        foreach ($started as $patch) {
            $patch->update(['status' => 'Active']);
            AuditService::updated('patch', $patch->patch_id, 'status', 'Upcoming', 'Active');
            $this->info("Activated: {$patch->name}");
        }

        $this->info('Done.');
    }
}