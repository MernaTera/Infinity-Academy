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
    /*
    |------------------------------------------------------------------
    | Teacher Availability — للـ modal
    |------------------------------------------------------------------
    */
    public function getTeacherAvailablePairs($teacherId): array
    {
        $availability = TeacherAvailability::with('timeSlot')
            ->where('teacher_id', $teacherId)
            ->get();

        $pairLabels = [
            'sun_wed' => 'Sun & Wed',
            'sat_tue' => 'Sat & Tue',
            'mon_thu' => 'Mon & Thu',
        ];

        $result = [];

        foreach ($availability as $record) {
            if (!$record->timeSlot) continue;

            $result[] = [
                'pair'      => $record->day_of_week,
                'label'     => $pairLabels[$record->day_of_week] ?? $record->day_of_week,
                'time_slot' => $record->timeSlot,
            ];
        }

        return $result;
    }

    private function pairLabel(string $pair): string
    {
        return match($pair) {
            'sun_wed' => 'Sun & Wed',
            'sat_tue' => 'Sat & Tue',
            'mon_thu' => 'Mon & Thu',
            default   => $pair,
        };
    }

    /*
    |------------------------------------------------------------------
    | Validate Schedule
    | start_time لازم جوا الـ time_slot وبعيد عن الـ breaks
    |------------------------------------------------------------------
    */
    public function validateSchedule(array $data): void
    {
        $startTime    = Carbon::createFromTimeString($data['start_time']);
        $slotStart    = Carbon::createFromTimeString($data['time_slot']->start_time);
        $slotEnd      = Carbon::createFromTimeString($data['time_slot']->end_time);
        $sessionEnd = $startTime->copy()->addHours((float) $data['session_duration']);

        // لازم جوا الـ slot
        if ($startTime->lt($slotStart) || $sessionEnd->gt($slotEnd)) {
            throw new \Exception(
                "Session time ({$startTime->format('H:i')} → {$sessionEnd->format('H:i')}) " .
                "must be within slot ({$slotStart->format('H:i')} → {$slotEnd->format('H:i')})"
            );
        }

        // مش في break
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
    | Store Schedule
    |------------------------------------------------------------------
    */
    public function storeSchedule(int $instanceId, array $data): InstanceSchedule
    {
        // امسح القديم لو موجود
        InstanceSchedule::where('course_instance_id', $instanceId)->delete();

        return InstanceSchedule::create([
            'course_instance_id'     => $instanceId,
            'day_of_week'            => $data['day_of_week'],
            'time_slot_id'           => $data['time_slot_id'],
            'start_time'             => $data['start_time'],
            'created_by_employee_id' => auth()->user()->employee->first()?->employee_id,
        ]);
    }

    /*
    |------------------------------------------------------------------
    | Generate Sessions
    | بيولد كل الـ CourseSession records بناءً على الـ schedule
    |------------------------------------------------------------------
    */
    public function generateSessions(CourseInstance $instance, InstanceSchedule $schedule): int
    {
        $dayMap = [
            'sun_wed' => [0, 3],
            'sat_tue' => [6, 2],
            'mon_thu' => [1, 4],
        ];

        $targetDays = $dayMap[$schedule->day_of_week] ?? [];

        if (empty($targetDays)) {
            throw new \Exception('Invalid day pair selected.');
        }

        // عدد الـ sessions المطلوبة
        $totalSessions = (int) ceil($instance->total_hours / $instance->session_duration);

        // امسح الـ sessions القديمة
        CourseSession::where('course_instance_id', $instance->course_instance_id)->delete();

        $current    = Carbon::parse($instance->start_date);
        $end        = Carbon::parse($instance->end_date);
        $sessionNum = 1;
        $generated  = 0;

        while ($current->lte($end) && $generated < $totalSessions) {

            if (in_array($current->dayOfWeek, $targetDays)) {

                $startDateTime = Carbon::parse(
                    $current->toDateString() . ' ' . Carbon::parse($schedule->start_time)->format('H:i:s')
                );
                $endDateTime = $startDateTime->copy()
                    ->addHours((float) $instance->session_duration);

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
    | Preview — للـ AJAX قبل الحفظ
    |------------------------------------------------------------------
    */
    public function previewSessions(CourseInstance $instance, string $dayOfWeek, string $startTime): array
    {
        $dayMap = [
            'sun_wed' => [0, 3],
            'sat_tue' => [6, 2],
            'mon_thu' => [1, 4],
        ];

        $targetDays    = $dayMap[$dayOfWeek] ?? [];
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
}