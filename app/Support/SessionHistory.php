<?php

namespace App\Support;

use App\Models\Academic\CourseInstance;
use App\Models\Attendance\Attendance;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class SessionHistory
{
    public static function kept(CourseInstance $instance): Collection
    {
        $sessions = $instance->sessions()->get();

        $attendedIds = Attendance::whereIn('course_session_id', $sessions->pluck('course_session_id'))
            ->distinct()
            ->pluck('course_session_id')
            ->all();

        $now = now();

        return $sessions->filter(function ($s) use ($attendedIds, $now) {
            if ($s->status === 'Cancelled') {
                return false;
            }
            if ($s->status === 'Completed' || in_array($s->course_session_id, $attendedIds)) {
                return true;
            }
            return self::startsAt($s)->lte($now);
        })->values();
    }

    public static function hours(Collection $sessions, float $fallbackDuration): float
    {
        return (float) $sessions->sum(function ($s) use ($fallbackDuration) {
            if ($s->start_time && $s->end_time) {
                $mins = Carbon::parse($s->start_time)->diffInMinutes(Carbon::parse($s->end_time));
                if ($mins > 0) {
                    return round($mins / 60, 2);
                }
            }
            return $fallbackDuration;
        });
    }

    public static function remainingToMake(float $totalHours, float $doneHours, float $duration): int
    {
        if ($duration <= 0) {
            return 0;
        }
        $left = round($totalHours - $doneHours, 2);

        return $left > 0 ? (int) ceil($left / $duration) : 0;
    }

    public static function startsAt($session): Carbon
    {
        $date = Carbon::parse($session->session_date)->toDateString();
        $time = Carbon::parse($session->start_time)->format('H:i:s');

        return Carbon::parse($date . ' ' . $time);
    }
}
