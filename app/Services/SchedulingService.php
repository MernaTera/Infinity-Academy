<?php

namespace App\Services;

use App\Models\Academic\InstanceSchedule;
use App\Models\Academic\CourseInstance;
use App\Models\Academic\CourseSession;
use App\Models\Academic\TimeSlot;
use App\Models\Academic\BreakSlot;
use App\Models\HR\TeacherAvailability;
use Carbon\Carbon;

class SchedulingService
{
    const DAY_MAP = [
        'sun_wed' => [0, 3],
        'sat_tue' => [6, 2],
        'mon_thu' => [1, 4],
    ];

    const PAIR_LABELS = [
        'sun_wed' => 'Sun & Wed',
        'sat_tue' => 'Sat & Tue',
        'mon_thu' => 'Mon & Thu',
    ];

    /*
    |------------------------------------------------------------------
    | Teacher Availability
    |------------------------------------------------------------------
    */
    public function getTeacherAvailablePairs($teacherId): array
    {
        $availability = TeacherAvailability::with('timeSlot')
            ->where('teacher_id', $teacherId)
            ->get();

        $result = [];

        foreach ($availability as $record) {
            if (!$record->timeSlot) continue;

            $result[] = [
                'pair'      => $record->day_of_week,
                'label'     => self::PAIR_LABELS[$record->day_of_week] ?? $record->day_of_week,
                'time_slot' => $record->timeSlot,
            ];
        }

        return $result;
    }

    private function pairLabel(string $pair): string
    {
        return self::PAIR_LABELS[$pair] ?? $pair;
    }

    /*
    |------------------------------------------------------------------
    | Validate Schedule
    |------------------------------------------------------------------
    */
    public function validateSchedule(array $data): void
    {
        $startTime  = Carbon::createFromTimeString($data['start_time']);
        $slotStart  = Carbon::createFromTimeString($data['time_slot']->start_time);
        $slotEnd    = Carbon::createFromTimeString($data['time_slot']->end_time);
        $sessionEnd = $startTime->copy()->addHours((float) $data['session_duration']);

        if ($startTime->lt($slotStart) || $sessionEnd->gt($slotEnd)) {
            throw new \Exception(
                "Session time ({$startTime->format('H:i')} → {$sessionEnd->format('H:i')}) " .
                "must be within slot ({$slotStart->format('H:i')} → {$slotEnd->format('H:i')})"
            );
        }

        $breaks = BreakSlot::all();
        foreach ($breaks as $break) {
            $breakStart = Carbon::createFromTimeString($break->start_time);
            $breakEnd   = Carbon::createFromTimeString($break->end_time);

            if ($startTime->lt($breakEnd) && $sessionEnd->gt($breakStart)) {
                throw new \Exception(
                    "Session overlaps with break time ({$breakStart->format('H:i')} → {$breakEnd->format('H:i')})"
                );
            }
        }
    }

    /*
    |------------------------------------------------------------------
    | Store Schedule — single pair record
    |------------------------------------------------------------------
    */
    public function storeSchedule(int $instanceId, array $data): InstanceSchedule
    {
        return InstanceSchedule::create([
            'course_instance_id'     => $instanceId,
            'day_of_week'            => $data['day_of_week'],
            'time_slot_id'           => $data['time_slot_id'] ?? null,
            'start_time'             => $data['start_time'],
            'created_by_employee_id' => auth()->user()->employee->first()?->employee_id,
        ]);
    }

    /*
    |------------------------------------------------------------------
    | Store Multiple Schedules (multi-pair)
    | الـ controller بيمسح القديم ويستدعي الـ method دي
    |------------------------------------------------------------------
    */
    public function storeMultipleSchedules(int $instanceId, array $pairs, string $startTime, ?int $timeSlotId): array
    {
        $schedules = [];
        foreach ($pairs as $pair) {
            $schedules[] = $this->storeSchedule($instanceId, [
                'day_of_week'  => $pair,
                'time_slot_id' => $timeSlotId,
                'start_time'   => $startTime,
            ]);
        }
        return $schedules;
    }

    /*
    |------------------------------------------------------------------
    | Generate Sessions — مع support للـ session_number counter
    | مش بتمسح — المسح بيكون في الـ controller قبل الـ loop
    |------------------------------------------------------------------
    */
    public function generateSessions(
        CourseInstance $instance,
        InstanceSchedule $schedule,
        int &$sessionNum = 1,
        int $remainingSessions = 0
    ): int {
        $targetDays = self::DAY_MAP[$schedule->day_of_week] ?? [];

        if (empty($targetDays)) {
            throw new \Exception('Invalid day pair: ' . $schedule->day_of_week);
        }

        $totalSessions = $remainingSessions > 0
            ? $remainingSessions
            : (int) ceil($instance->total_hours / $instance->session_duration);

        $current   = Carbon::parse($instance->start_date);
        $end       = Carbon::parse($instance->end_date);
        $generated = 0;

        while ($current->lte($end) && $generated < $totalSessions) {
            if (in_array($current->dayOfWeek, $targetDays)) {
                $startDateTime = Carbon::parse(
                    $current->toDateString() . ' ' . Carbon::parse($schedule->start_time)->format('H:i:s')
                );
                $endDateTime = $startDateTime->copy()->addHours((float) $instance->session_duration);

                CourseSession::create([
                    'course_instance_id'      => $instance->course_instance_id,
                    'session_date'            => $current->toDateString(),
                    'start_time'              => $startDateTime,
                    'end_time'                => $endDateTime,
                    'session_number'          => $sessionNum,
                    'room_id'                 => $instance->room_id,
                    'generated_from_schedule' => true,
                    'status'                  => 'Scheduled',
                ]);

                $sessionNum++;
                $generated++;
            }
            $current->addDay();
        }

        return $generated;
    }

    /*
    |------------------------------------------------------------------
    | Generate Sessions for Multiple Pairs
    | الـ method الرئيسية لما في أكتر من pair
    | بتوزع الـ sessions بالتساوي على الـ pairs
    |------------------------------------------------------------------
    */
    public function generateSessionsMultiPair(CourseInstance $instance, array $schedules): int
    {
        // امسح القديم
        CourseSession::where('course_instance_id', $instance->course_instance_id)->delete();

        $totalSessions = (int) ceil($instance->total_hours / $instance->session_duration);
        $pairCount     = count($schedules);

        // وزّع الـ sessions على الـ pairs بالتساوي
        $perPair   = (int) floor($totalSessions / $pairCount);
        $remainder = $totalSessions % $pairCount;

        $sessionNum    = 1;
        $totalGenerated = 0;

        foreach ($schedules as $i => $schedule) {
            // الـ pair الأول بياخد الـ remainder
            $sessionsForThisPair = $perPair + ($i === 0 ? $remainder : 0);

            $generated = $this->generateSessions(
                $instance,
                $schedule,
                $sessionNum,
                $sessionsForThisPair
            );

            $totalGenerated += $generated;
        }

        // Sort sessions by date and re-number
        $this->renumberSessions($instance->course_instance_id);

        return $totalGenerated;
    }

    /*
    |------------------------------------------------------------------
    | Re-number sessions by date after multi-pair generation
    |------------------------------------------------------------------
    */
    private function renumberSessions(int $instanceId): void
    {
        $sessions = CourseSession::where('course_instance_id', $instanceId)
            ->orderBy('session_date')
            ->orderBy('start_time')
            ->get();

        foreach ($sessions as $session) {
            \DB::table('course_session')
                ->where('course_session_id', $session->course_session_id)
                ->update(['session_number' => -$session->course_session_id]);
        }

        foreach ($sessions as $i => $session) {
            \DB::table('course_session')
                ->where('course_session_id', $session->course_session_id)
                ->update(['session_number' => $i + 1]);
        }
    }

    /*
    |------------------------------------------------------------------
    | Preview — يدعم multi-pair
    |------------------------------------------------------------------
    */
    public function previewSessions(CourseInstance $instance, string $dayOfWeek, string $startTime): array
    {
        $targetDays    = self::DAY_MAP[$dayOfWeek] ?? [];
        $totalSessions = (int) ceil($instance->total_hours / $instance->session_duration);

        $current   = Carbon::parse($instance->start_date);
        $end       = Carbon::parse($instance->end_date);
        $count     = 0;
        $firstDate = null;
        $lastDate  = null;

        while ($current->lte($end) && $count < $totalSessions) {
            if (in_array($current->dayOfWeek, $targetDays)) {
                if (!$firstDate) $firstDate = $current->toDateString();
                $lastDate = $current->toDateString();
                $count++;
            }
            $current->addDay();
        }

        return [
            'total_sessions'   => $count,
            'first_session'    => $firstDate,
            'last_session'     => $lastDate,
            'session_duration' => $instance->session_duration,
            'start_time'       => $startTime,
            'end_time'         => Carbon::createFromTimeString($startTime)
                                    ->addHours((float) $instance->session_duration)
                                    ->format('H:i'),
        ];
    }

    /*
    |------------------------------------------------------------------
    | Preview Multi-Pair
    |------------------------------------------------------------------
    */
    public function previewMultiPair(
        string $startDate,
        string $endDate,
        array  $pairs,
        float  $totalHours,
        float  $sessionDuration,
        string $startTime
    ): array {
        $totalSessions = (int) ceil($totalHours / $sessionDuration);
        $allTargetDays = array_merge(...array_map(fn($p) => self::DAY_MAP[$p] ?? [], $pairs));

        $current   = Carbon::parse($startDate);
        $end       = Carbon::parse($endDate);
        $count     = 0;
        $firstDate = null;
        $lastDate  = null;

        while ($current->lte($end) && $count < $totalSessions) {
            if (in_array($current->dayOfWeek, $allTargetDays)) {
                if (!$firstDate) $firstDate = $current->toDateString();
                $lastDate = $current->toDateString();
                $count++;
            }
            $current->addDay();
        }

        $endTime = Carbon::createFromTimeString($startTime)
            ->addHours($sessionDuration)
            ->format('H:i');

        return [
            'total_sessions' => $count,
            'first_session'  => $firstDate,
            'last_session'   => $lastDate,
            'end_time'       => $endTime,
            'pairs'          => array_map(fn($p) => self::PAIR_LABELS[$p] ?? $p, $pairs),
        ];
    }
}