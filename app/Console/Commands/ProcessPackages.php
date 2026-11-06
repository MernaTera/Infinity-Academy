<?php

namespace App\Console\Commands;

use App\Models\Attendance\Attendance;
use App\Models\Enrollment\Enrollment;
use App\Models\Finance\RefundRequest;
use App\Services\PackageProgressionService;
use Carbon\Carbon;
use Illuminate\Console\Command;

class ProcessPackages extends Command
{
    protected $signature   = 'packages:process';
    protected $description = 'Move package students to their next level when a level is completed, and re-enroll students removed for absences into the same level once attendance can no longer be edited.';

    private const EDIT_WINDOW_MINUTES = 20;

    public function handle(PackageProgressionService $progression): void
    {
        $completed = Enrollment::withoutGlobalScope('branch')
            ->where('status', 'Completed')
            ->where('enrollment_type', 'Group')
            ->whereNotNull('package_id')
            ->where('package_units_remaining', '>', 0)
            ->get();

        $advanced = 0;
        foreach ($completed as $enrollment) {
            if ($progression->advance($enrollment)) {
                $advanced++;
            }
        }

        if ($advanced > 0) {
            $this->info("Moved {$advanced} package student(s) to their next level.");
        }

        $candidates = Enrollment::withoutGlobalScope('branch')
            ->where('status', 'Cancelled')
            ->where('enrollment_type', 'Group')
            ->whereNotNull('package_id')
            ->where('package_units_remaining', '>', 0)
            ->whereNotNull('course_instance_id')
            ->get();

        $now = Carbon::now();
        $processed = 0;

        foreach ($candidates as $enrollment) {
            $refunded = RefundRequest::where('enrollment_id', $enrollment->enrollment_id)
                ->whereIn('status', ['Approved', 'Processed'])
                ->exists();

            if ($refunded) {
                continue;
            }

            $absences = Attendance::where('enrollment_id', $enrollment->enrollment_id)
                ->where('status', 'Absent')
                ->with('session')
                ->get();

            if ($absences->count() < PackageProgressionService::ABSENCE_LIMIT) {
                continue;
            }

            $lastSession = $absences
                ->pluck('session')
                ->filter()
                ->sortByDesc(fn($s) => Carbon::parse($s->session_date)->format('Y-m-d') . ' ' . Carbon::parse($s->start_time)->format('H:i:s'))
                ->first();

            if (!$lastSession) {
                continue;
            }

            $windowClosesAt = Carbon::parse(
                Carbon::parse($lastSession->session_date)->format('Y-m-d') . ' ' . Carbon::parse($lastSession->start_time)->format('H:i:s')
            )->addMinutes(self::EDIT_WINDOW_MINUTES);

            if ($windowClosesAt->gt($now)) {
                continue;
            }

            if ($progression->repeat($enrollment)) {
                $processed++;
            }
        }

        if ($processed > 0) {
            $this->info("Re-enrolled {$processed} package student(s) for a repeat level.");
        }
    }
}
