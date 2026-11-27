<?php

namespace App\Services;

use App\Events\WaitingListUpdated;
use App\Models\Academic\CourseSession;
use App\Models\Academic\Level;
use App\Models\Academic\Patch;
use App\Models\Academic\Sublevel;
use App\Models\Enrollment\Enrollment;
use App\Models\Enrollment\RestrictionLog;
use App\Models\Enrollment\WaitingList;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PrivateLevelService
{
    public function __construct(private PackageProgressionService $progression)
    {
    }

    public function unitLabel(?int $levelId, ?int $sublevelId): ?string
    {
        if (!$levelId) {
            return null;
        }
        $label = Level::whereKey($levelId)->value('name');
        if ($sublevelId) {
            $sub = Sublevel::whereKey($sublevelId)->value('name');
            if ($sub) $label .= ' · ' . $sub;
        }
        return $label;
    }

    public function nextUnit(Enrollment $enrollment): ?array
    {
        return $this->progression->nextUnit($enrollment);
    }

    public function courseFinished(Enrollment $enrollment): bool
    {
        if (!$enrollment->course_instance_id) {
            return false;
        }

        $sessions = CourseSession::where('course_instance_id', $enrollment->course_instance_id);
        if (!(clone $sessions)->exists()) {
            return false;
        }

        return !(clone $sessions)->whereNotIn('status', ['Completed', 'Cancelled'])->exists();
    }

    public function hasPaymentRestriction(Enrollment $enrollment): bool
    {
        return RestrictionLog::where('enrollment_id', $enrollment->enrollment_id)
            ->whereNull('released_at')
            ->exists();
    }

    public function handleCourseEnd(Enrollment $enrollment): void
    {
        if ($enrollment->enrollment_type !== 'Private' || $enrollment->package_id) {
            return;
        }

        if ((float) $enrollment->hours_remaining <= 0) {
            if ($enrollment->status === 'Restricted' && !$this->hasPaymentRestriction($enrollment)) {
                $old = $enrollment->status;
                $enrollment->status = 'Completed';
                $enrollment->restriction_flag = false;
                $enrollment->save();
                AuditService::updated('enrollment', $enrollment->enrollment_id, 'status', $old, 'Completed');
            }
            return;
        }

        if ($enrollment->status !== 'Completed') {
            return;
        }

        if ($this->nextUnit($enrollment)) {
            $hours = rtrim(rtrim(number_format((float) $enrollment->hours_remaining, 2), '0'), '.');
            $this->moveToWaitingList($enrollment, "{$hours} hours left, moved automatically on " . now()->format('d M Y') . '.');
        }
    }

    public function moveToWaitingList(Enrollment $enrollment, string $note): WaitingList
    {
        $oldStatus = $enrollment->status;

        if (!$enrollment->package_id && ($oldStatus === 'Completed' || $this->courseFinished($enrollment))) {
            $instance  = $enrollment->courseInstance()->withoutGlobalScopes()->first();
            $doneLabel = $this->unitLabel(
                $enrollment->level_id ?? $instance?->level_id,
                $enrollment->sublevel_id ?? $instance?->sublevel_id
            );
            $next = $this->nextUnit($enrollment);
            if ($next) {
                $enrollment->level_id    = $next['level_id'];
                $enrollment->sublevel_id = $next['sublevel_id'];
                $note = ($doneLabel ? $doneLabel . ' completed — ' : '')
                    . 'continuing on ' . $this->unitLabel($next['level_id'], $next['sublevel_id']) . '. ' . $note;
            } else {
                $note = ($doneLabel ? $doneLabel . ' completed — ' : '')
                    . 'last level of the course, choose the next course. ' . $note;
            }
        }

        $nextPatch = Patch::withoutGlobalScope('branch')
            ->where('branch_id', $enrollment->branch_id)
            ->where('status', 'Upcoming')
            ->orderBy('start_date')
            ->first();

        $enrollment->status             = 'Waiting';
        $enrollment->course_instance_id = null;
        $enrollment->restriction_flag   = false;
        $enrollment->patch_id           = $nextPatch?->patch_id ?? $enrollment->patch_id;
        $enrollment->save();

        $existing = WaitingList::withoutGlobalScopes()->where('enrollment_id', $enrollment->enrollment_id)->first();

        $waiting = WaitingList::withoutGlobalScopes()->updateOrCreate(
            ['enrollment_id' => $enrollment->enrollment_id],
            [
                'requested_patch_id'      => $nextPatch?->patch_id,
                'preferred_type'          => 'Next_Patch',
                'preferred_delivery_type' => 'Private',
                'preferred_delivery_mood' => $enrollment->delivery_mood,
                'preferred_days'          => $existing?->preferred_days,
                'status'                  => 'Active',
                'notes'                   => trim(($existing?->notes ? $existing->notes . "\n" : '') . $note),
                'created_by_cs_id'        => $existing?->created_by_cs_id ?? $enrollment->created_by_cs_id,
            ]
        );

        AuditService::updated('enrollment', $enrollment->enrollment_id, 'status', $oldStatus, 'Waiting');

        DB::afterCommit(function () use ($waiting) {
            try {
                event(new WaitingListUpdated($waiting));
            } catch (\Throwable $e) {
                Log::warning('WaitingListUpdated broadcast failed: ' . $e->getMessage());
            }
        });

        return $waiting;
    }
}
