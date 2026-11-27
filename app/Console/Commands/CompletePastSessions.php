<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\Academic\CourseSession;
use App\Models\Enrollment\Enrollment;
use App\Services\PackageProgressionService;
use Carbon\Carbon;

class CompletePastSessions extends Command
{
    protected $signature   = 'sessions:complete-past';
    protected $description = 'Mark Scheduled sessions whose end time has passed as Completed and cascade course completion.';

    public function handle(): void
    {
        $now = Carbon::now();

        $sessions = CourseSession::where('status', 'Scheduled')
            ->whereDate('session_date', '<=', $now->toDateString())
            ->get();

        $affected = [];

        foreach ($sessions as $session) {
            $dateStr    = Carbon::parse($session->session_date)->format('Y-m-d');
            $timeStr    = Carbon::parse($session->end_time ?: $session->start_time)->format('H:i:s');
            $sessionEnd = Carbon::parse($dateStr . ' ' . $timeStr);

            if ($sessionEnd->lt($now)) {
                $session->update(['status' => 'Completed']);
                $affected[$session->course_instance_id] = true;
            }
        }

        foreach (array_keys($affected) as $instanceId) {
            $remaining = CourseSession::where('course_instance_id', $instanceId)
                ->whereNotIn('status', ['Completed', 'Cancelled'])
                ->count();

            if ($remaining === 0) {
                $finishing = Enrollment::withoutGlobalScope('branch')
                    ->where('course_instance_id', $instanceId)
                    ->where('status', 'Active')
                    ->where(function ($q) {
                        $q->where(function ($p) {
                            $p->where('enrollment_type', 'Private')
                              ->where('hours_remaining', '>', 0);
                        })->orWhere(function ($p) {
                            $p->whereNotNull('package_id');
                        })->orWhere(function ($p) {
                            $p->whereNull('package_id')
                              ->where('enrollment_type', '!=', 'Private');
                        });
                    })
                    ->get();

                $depleted = Enrollment::withoutGlobalScope('branch')
                    ->where('course_instance_id', $instanceId)
                    ->where('enrollment_type', 'Private')
                    ->whereNull('package_id')
                    ->where('status', 'Restricted')
                    ->where('hours_remaining', '<=', 0)
                    ->get();

                $privateLevels = app(\App\Services\PrivateLevelService::class);

                foreach ($depleted as $e) {
                    $privateLevels->handleCourseEnd($e);
                }

                if ($finishing->isEmpty()) {
                    continue;
                }

                Enrollment::withoutGlobalScope('branch')
                    ->whereIn('enrollment_id', $finishing->pluck('enrollment_id'))
                    ->update(['status' => 'Completed']);

                $progression = app(PackageProgressionService::class);

                $finishing
                    ->filter(fn($e) => $e->package_id && (int) $e->package_units_remaining > 0)
                    ->each(fn($e) => $progression->advance($e));

                $finishing
                    ->filter(fn($e) => $e->enrollment_type === 'Private' && !$e->package_id)
                    ->each(function ($e) use ($privateLevels) {
                        try {
                            DB::transaction(function () use ($e, $privateLevels) {
                                $fresh = Enrollment::withoutGlobalScope('branch')->lockForUpdate()->find($e->enrollment_id);
                                if ($fresh) {
                                    $privateLevels->handleCourseEnd($fresh);
                                }
                            });
                        } catch (\Throwable $ex) {
                            \Illuminate\Support\Facades\Log::error('Private level move failed for enrollment ' . $e->enrollment_id . ': ' . $ex->getMessage());
                        }
                    });
            }
        }
    }
}
