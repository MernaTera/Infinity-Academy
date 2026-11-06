<?php

namespace App\Services;

use App\Events\WaitingListUpdated;
use App\Models\Academic\Level;
use App\Models\Academic\Patch;
use App\Models\Academic\Sublevel;
use App\Models\Enrollment\Enrollment;
use App\Models\Enrollment\WaitingList;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PackageProgressionService
{
    public const ABSENCE_LIMIT = 3;

    public function advance(Enrollment $current): ?Enrollment
    {
        return $this->progress($current, 'Next_Level');
    }

    public function repeat(Enrollment $current): ?Enrollment
    {
        return $this->progress($current, 'Repeat');
    }

    public function nextUnit(Enrollment $current): ?array
    {
        $courseId = $this->courseId($current);
        $levelId  = $this->levelId($current);

        if (!$courseId || !$levelId) {
            return null;
        }

        $levelHasSublevels = Sublevel::where('level_id', $levelId)->exists();

        if ($levelHasSublevels && $current->sublevel_id) {
            $currentSub = Sublevel::find($current->sublevel_id);
            if ($currentSub) {
                $nextSub = Sublevel::where('level_id', $levelId)
                    ->where('sublevel_order', '>', $currentSub->sublevel_order)
                    ->orderBy('sublevel_order')
                    ->first();
                if ($nextSub) {
                    return ['level_id' => $levelId, 'sublevel_id' => $nextSub->sublevel_id];
                }
            }

            $nextLevel = $this->nextLevel($courseId, $levelId);
            if ($nextLevel) {
                $firstSub = Sublevel::where('level_id', $nextLevel->level_id)
                    ->orderBy('sublevel_order')
                    ->first();

                return ['level_id' => $nextLevel->level_id, 'sublevel_id' => $firstSub?->sublevel_id];
            }

            return null;
        }

        $nextLevel = $this->nextLevel($courseId, $levelId);

        return $nextLevel ? ['level_id' => $nextLevel->level_id, 'sublevel_id' => null] : null;
    }

    private function progress(Enrollment $current, string $type): ?Enrollment
    {
        try {
            return DB::transaction(function () use ($current, $type) {
                $source = Enrollment::withoutGlobalScope('branch')
                    ->lockForUpdate()
                    ->find($current->enrollment_id);

                if (!$source || !$source->package_id || (int) $source->package_units_remaining <= 0) {
                    return null;
                }

                if ($type === 'Repeat') {
                    $unit = [
                        'level_id'    => $this->levelId($source),
                        'sublevel_id' => $source->sublevel_id,
                    ];
                } else {
                    $unit = $this->nextUnit($source);
                }

                if (!$unit || !$unit['level_id']) {
                    return null;
                }

                $nextPatch = Patch::withoutGlobalScope('branch')
                    ->where('branch_id', $source->branch_id)
                    ->where('status', 'Upcoming')
                    ->orderBy('start_date')
                    ->first();

                $enrollment = Enrollment::create([
                    'student_id'              => $source->student_id,
                    'course_template_id'      => $this->courseId($source),
                    'course_instance_id'      => null,
                    'level_id'                => $unit['level_id'],
                    'sublevel_id'             => $unit['sublevel_id'],
                    'patch_id'                => $nextPatch?->patch_id ?? $source->patch_id,
                    'branch_id'               => $source->branch_id,
                    'teacher_id'              => null,
                    'enrollment_type'         => 'Group',
                    'delivery_mood'           => $source->delivery_mood,
                    'final_price'             => 0,
                    'payment_plan_id'         => $source->payment_plan_id,
                    'bundle_id'               => null,
                    'package_id'              => $source->package_id,
                    'package_units_remaining' => (int) $source->package_units_remaining - 1,
                    'package_progression'     => $type,
                    'previous_enrollment_id'  => $source->enrollment_id,
                    'hours_remaining'         => null,
                    'discount_value'          => 0,
                    'status'                  => 'Waiting',
                    'created_by_cs_id'        => $source->created_by_cs_id,
                ]);

                $source->package_units_remaining = 0;
                $source->save();

                $position = $enrollment->packagePosition();
                $note = $type === 'Repeat'
                    ? 'Repeating level — absence limit reached (package ' . $position['current'] . ' of ' . $position['total'] . ').'
                    : 'Package ' . $position['current'] . ' of ' . $position['total'] . ' — next level, continued automatically.';

                $waiting = WaitingList::create([
                    'enrollment_id'           => $enrollment->enrollment_id,
                    'requested_patch_id'      => $nextPatch?->patch_id,
                    'preferred_type'          => 'Next_Patch',
                    'preferred_delivery_type' => 'Group',
                    'preferred_delivery_mood' => $source->delivery_mood,
                    'status'                  => 'Active',
                    'notes'                   => $note,
                    'created_by_cs_id'        => $source->created_by_cs_id,
                ]);

                DB::afterCommit(function () use ($waiting) {
                    try {
                        event(new WaitingListUpdated($waiting));
                    } catch (\Throwable $e) {
                        Log::warning('WaitingListUpdated broadcast failed: ' . $e->getMessage());
                    }
                });

                return $enrollment;
            });
        } catch (\Throwable $e) {
            Log::error('Package progression failed for enrollment ' . $current->enrollment_id . ': ' . $e->getMessage());
            return null;
        }
    }

    private function courseId(Enrollment $e): ?int
    {
        return $e->course_template_id
            ?? $e->courseInstance()->withoutGlobalScopes()->value('course_template_id');
    }

    private function levelId(Enrollment $e): ?int
    {
        return $e->level_id
            ?? $e->courseInstance()->withoutGlobalScopes()->value('level_id');
    }

    private function nextLevel(int $courseId, int $currentLevelId): ?Level
    {
        $currentLevel = Level::find($currentLevelId);
        if (!$currentLevel) {
            return null;
        }

        return Level::where('course_template_id', $courseId)
            ->where('level_order', '>', $currentLevel->level_order)
            ->orderBy('level_order')
            ->first();
    }
}
