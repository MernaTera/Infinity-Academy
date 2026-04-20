<?php
namespace App\Http\Controllers\Teacher;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;


class TeacherController extends Controller
{
    public function dashboard() { return view('teacher.dashboard'); }
    public function schedule(Request $request)
    {
        $teacher = \App\Models\HR\Teacher::where('employee_id',
            \App\Models\HR\Employee::where('user_id', auth()->id())->first()?->employee_id
        )->first();

        if (!$teacher) abort(404);

        $currentPatch = \App\Models\Academic\Patch::where('status', 'Active')
            ->latest('start_date')->first();

        // جيب الـ course instances بتاعت الـ teacher في الـ current patch
        $instances = \App\Models\Academic\CourseInstance::with([
            'courseTemplate',
            'level',
            'sublevel',
            'branch',
            'room',
            'instanceSchedules.timeSlot',
            'sessions',
            'enrollments.student',
        ])
        ->where('teacher_id', $teacher->teacher_id)
        ->where('patch_id', $currentPatch?->patch_id)
        ->whereIn('status', ['Active', 'Upcoming', 'Completed'])
        ->get();

        // Filter
        $filterPair = $request->query('pair');
        $filterSlot = $request->query('slot');

        $filtered = $instances->filter(function ($inst) use ($filterPair, $filterSlot) {
            $schedule = $inst->instanceSchedules->first();
            if ($filterPair && $schedule?->day_of_week !== $filterPair) return false;
            if ($filterSlot && $schedule?->time_slot_id != $filterSlot) return false;
            return true;
        });

        $timeSlots = \App\Models\Academic\TimeSlot::where('is_active', true)->get();

        return view('teacher.schedule', compact(
            'currentPatch', 'instances', 'filtered',
            'timeSlots', 'filterPair', 'filterSlot'
        ));
    }
    public function courses()
    {
        $teacher = \App\Models\HR\Teacher::where('employee_id',
            \App\Models\HR\Employee::where('user_id', auth()->id())->first()?->employee_id
        )->first();

        if (!$teacher) abort(404);

        $activeCourses = \App\Models\Academic\CourseInstance::with([
            'courseTemplate', 'level', 'sublevel', 'patch',
            'instanceSchedules.timeSlot', 'sessions', 'enrollments',
        ])
        ->where('teacher_id', $teacher->teacher_id)
        ->whereIn('status', ['Active', 'Upcoming'])
        ->orderByDesc('start_date')->get();

        $completedCourses = \App\Models\Academic\CourseInstance::with([
            'courseTemplate', 'level', 'patch', 'sessions',
        ])
        ->where('teacher_id', $teacher->teacher_id)
        ->where('status', 'Completed')
        ->orderByDesc('end_date')->limit(10)->get();

        $stats = [
            'active'    => $activeCourses->where('status','Active')->count(),
            'upcoming'  => $activeCourses->where('status','Upcoming')->count(),
            'completed' => $completedCourses->count(),
            'students'  => $activeCourses->sum(fn($i) => $i->enrollments->count()),
        ];

        return view('teacher.courses', compact('activeCourses', 'completedCourses', 'stats'));
    }

    public function courseShow($id)
    {
        $teacher = \App\Models\HR\Teacher::where('employee_id',
            \App\Models\HR\Employee::where('user_id', auth()->id())->first()?->employee_id
        )->first();

        $instance = \App\Models\Academic\CourseInstance::with([
            'courseTemplate', 'level', 'sublevel', 'patch',
            'instanceSchedules.timeSlot', 'branch', 'room',
            'sessions' => fn($q) => $q->orderBy('session_number'),
            'enrollments.student.phones',
            'enrollments.attendances',
            'enrollments.placementTest',
        ])
        ->where('teacher_id', $teacher->teacher_id)
        ->findOrFail($id);

        // Today's active session (within first 20 mins)
        $todaySession = $instance->sessions->first(function($s) {
            if ($s->status !== 'Scheduled') return false;
            if (!\Carbon\Carbon::parse($s->session_date)->isToday()) return false;
            $start    = \Carbon\Carbon::parse($s->start_time);
            $deadline = $start->copy()->addMinutes(20);
            return now()->between($start, $deadline);
        });

        // Calc attendance stats per enrollment
        $totalSessions = $instance->sessions->count();
        $completedSessions = $instance->sessions->where('status','Completed')->count();

        return view('teacher.course-show', compact(
            'instance', 'todaySession', 'totalSessions', 'completedSessions'
        ));
    }
}