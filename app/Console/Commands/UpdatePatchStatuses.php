<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Academic\Patch;
use App\Models\Academic\CourseInstance;
use App\Services\AuditService;
use Carbon\Carbon;

class UpdatePatchStatuses extends Command
{
    protected $signature   = 'patches:update-statuses';
    protected $description = 'Auto-close expired patches, auto-activate patches/courses that have started, and complete courses that have ended';

    public function handle(): void
    {
        $today = Carbon::today();

        $expired = Patch::where('status', 'Active')
            ->where('end_date', '<', $today)
            ->get();

        foreach ($expired as $patch) {
            $patch->update(['status' => 'Closed', 'is_locked' => true]);
            AuditService::updated('patch', $patch->patch_id, 'status', 'Active', 'Closed');
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

        // Course instances follow the same rule: once a course's start date
        // has arrived it becomes Active on its own (across all branches).
        $startedInstances = CourseInstance::withoutGlobalScope('branch')
            ->where('status', 'Upcoming')
            ->whereDate('start_date', '<=', $today)
            ->get();

        foreach ($startedInstances as $instance) {
            $instance->update(['status' => 'Active']);
            AuditService::updated('course_instance', $instance->course_instance_id, 'status', 'Upcoming', 'Active');
        }
        $this->info('Activated ' . $startedInstances->count() . ' course instance(s).');

        // Courses whose end date has passed — and that have no non-cancelled
        // session still to run (postponed courses keep running) — auto-complete
        // and leave the Active tab (across all branches).
        $endedInstances = CourseInstance::withoutGlobalScope('branch')
            ->where('status', 'Active')
            ->whereDate('end_date', '<', $today)
            ->whereDoesntHave('sessions', function ($q) use ($today) {
                $q->where('status', '!=', 'Cancelled')
                  ->whereDate('session_date', '>=', $today);
            })
            ->get();

        foreach ($endedInstances as $instance) {
            $instance->update(['status' => 'Completed']);
            AuditService::updated('course_instance', $instance->course_instance_id, 'status', 'Active', 'Completed');
        }
        $this->info('Completed ' . $endedInstances->count() . ' course instance(s).');

        $this->info('Done.');
    }
}