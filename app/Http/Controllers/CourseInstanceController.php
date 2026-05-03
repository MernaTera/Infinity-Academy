<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Academic\CourseInstance;
use App\Models\Academic\CourseTemplate;
use App\Models\HR\Teacher;
use App\Models\Academic\Patch;
use App\Models\Core\Branch;
use App\Models\Academic\Level;
use App\Models\Academic\SubLevel;
use App\Models\Academic\Room;
use App\Services\SchedulingService;
use App\Models\Academic\TimeSlot;
use App\Models\Academic\BreakSlot;

class CourseInstanceController extends Controller
{
    protected $schedulingService;

    public function __construct(SchedulingService $schedulingService)
    {
        $this->schedulingService = $schedulingService;
    }

    // ────────────────────────────────────────────────────────────
    // Index
    // ────────────────────────────────────────────────────────────
    public function index()
    {
        $instances = CourseInstance::with([
            'courseTemplate','level','sublevel','teacher','patch','enrollments','sessions','instanceSchedules',
        ])->latest()->paginate(10);

        $templates = CourseTemplate::all();
        $teachers  = Teacher::with('employee')->get();
        $patches   = Patch::whereIn('status', ['Active', 'Upcoming'])->get();
        $branches  = Branch::all();
        $rooms     = Room::all();

        return view('student-care.course-instances.index', compact(
            'instances','templates','teachers','patches','branches','rooms'
        ));
    }

    // ────────────────────────────────────────────────────────────
    // Create page
    // ────────────────────────────────────────────────────────────
    public function create()
    {
        $templates  = CourseTemplate::orderBy('name')->get();
        $patches    = Patch::whereIn('status', ['Active', 'Upcoming'])->get();
        $branches   = Branch::orderBy('name')->get();
        $rooms      = Room::where('is_active', true)->orderBy('name')->get();
        $breakSlots = BreakSlot::where('is_active', true)->get(['start_time', 'end_time']);
        $employee   = \App\Models\HR\Employee::where('user_id', auth()->id())->first();
        $userBranch = Branch::find($employee->branch_id);
        return view('student-care.course-instances.create', compact(
            'templates', 'patches', 'branches', 'rooms', 'breakSlots','userBranch',
        ));
    }

    // ────────────────────────────────────────────────────────────
    // Store Instance + Schedule + Sessions (ONE form)
    // ────────────────────────────────────────────────────────────
    public function storeInstance(Request $request)
    {
        $data = $request->validate([
            'course_template_id' => 'required|exists:course_template,course_template_id',
            'level_id'           => 'nullable|exists:level,level_id',
            'sublevel_id'        => 'nullable|exists:sublevel,sublevel_id',
            'patch_id'           => 'required|exists:patch,patch_id',
            'teacher_id'         => 'required|exists:teacher,teacher_id',
            'branch_id'          => 'required|exists:branch,branch_id',
            'room_id'            => 'nullable|exists:room,room_id',
            'capacity'           => 'required|integer|min:1',
            'delivery_mood'      => 'required|in:Online,Offline',
            'type'               => 'required|in:Group,Private',
            'total_hours'        => 'required|numeric|min:1',
            'session_duration'   => 'required|numeric|min:0.5',
            'start_date'         => 'required|date',
            'end_date'           => 'required|date|after:start_date',
            'day_of_week'        => 'required|array|min:1',
            'day_of_week.*'      => 'in:sun_wed,sat_tue,mon_thu',
            'start_time'         => 'required|date_format:H:i',
            'time_slot_id'       => 'nullable|exists:time_slot,time_slot_id',
        ]);

        $employeeId = \App\Models\HR\Employee::where('user_id', auth()->id())->value('employee_id');

        try {
            \Illuminate\Support\Facades\DB::transaction(function () use ($data, $employeeId) {

                // 1) Create instance
                $instance = CourseInstance::create([
                    'course_template_id'     => $data['course_template_id'],
                    'level_id'               => $data['level_id'] ?? null,
                    'sublevel_id'            => $data['sublevel_id'] ?? null,
                    'patch_id'               => $data['patch_id'],
                    'teacher_id'             => $data['teacher_id'],
                    'branch_id'              => $data['branch_id'] ?? null,
                    'room_id'                => $data['room_id'] ?? null,
                    'capacity'               => $data['capacity'],
                    'delivery_mood'          => $data['delivery_mood'],
                    'type'                   => $data['type'],
                    'total_hours'            => $data['total_hours'],
                    'session_duration'       => $data['session_duration'],
                    'start_date'             => $data['start_date'],
                    'end_date'               => $data['end_date'],
                    'status'                 => 'Upcoming',
                    'created_by_employee_id' => $employeeId,
                ]);

                // 2) Validate time vs slot
                if (!empty($data['time_slot_id'])) {
                    $slot = TimeSlot::find($data['time_slot_id']);
                    if ($slot) {
                        $this->schedulingService->validateSchedule([
                            'start_time'       => $data['start_time'],
                            'session_duration' => $data['session_duration'],
                            'time_slot'        => $slot,
                        ]);
                    }
                }

                // 3) Clear old (safety for re-runs)
                \App\Models\Academic\InstanceSchedule::where('course_instance_id', $instance->course_instance_id)->delete();
                \App\Models\Academic\CourseSession::where('course_instance_id', $instance->course_instance_id)->delete();

                // 4) Store schedule per pair
                $schedules = $this->schedulingService->storeMultipleSchedules(
                    $instance->course_instance_id,
                    $data['day_of_week'],
                    $data['start_time'],
                    $data['time_slot_id'] ?? null
                );

                // 5) Generate sessions distributed across all pairs
                $this->schedulingService->generateSessionsMultiPair($instance, $schedules);
            });

        } catch (\Exception $e) {
            return back()->withInput()->withErrors(['schedule' => $e->getMessage()]);
        }

        return redirect()->route('student-care.instances')
            ->with('success', 'Course instance created successfully with schedule and sessions.');
    }

    // ────────────────────────────────────────────────────────────
    // Show
    // ────────────────────────────────────────────────────────────
    public function show($id)
    {
        $instance = CourseInstance::with([
            'courseTemplate','level','sublevel','teacher.employee',
            'branch','patch','room',
            'enrollments.student.phones',
            'sessions',
            'instanceSchedules.timeSlot',
            'enrollments.activePostponement',
        ])->findOrFail($id);

        return view('student-care.course-instances.show', compact('instance'));
    }

    // ────────────────────────────────────────────────────────────
    // AJAX: Teachers
    // ────────────────────────────────────────────────────────────
    public function getTeachersByCourse($courseId)
    {
        $course = CourseTemplate::find($courseId);
        if (!$course || !$course->english_level_id) return response()->json([]);

        return response()->json(
            Teacher::where('english_level_id', '>=', $course->english_level_id)->with('employee')->get()
        );
    }

    public function getTeachersByLevel($englishLevelId)
    {
        return response()->json(
            Teacher::where('english_level_id', '>=', $englishLevelId)
                ->where('is_active', true)
                ->with(['employee', 'englishLevel'])
                ->get()
        );
    }

    // ────────────────────────────────────────────────────────────
    // AJAX: Schedule data (for old schedule modal)
    // ────────────────────────────────────────────────────────────
    public function getScheduleData($id)
    {
        $instance = CourseInstance::with('teacher')->findOrFail($id);
        $pairs    = $this->schedulingService->getTeacherAvailablePairs($instance->teacher_id);

        return response()->json([
            'pairs'    => $pairs,
            'instance' => [
                'total_hours'      => $instance->total_hours,
                'session_duration' => $instance->session_duration,
                'start_date'       => $instance->start_date,
                'end_date'         => $instance->end_date,
            ],
        ]);
    }

    // ────────────────────────────────────────────────────────────
    // AJAX: Time slots for pair
    // ────────────────────────────────────────────────────────────
    public function getTimeSlotsForPair(Request $request)
    {
        $teacherId = $request->query('teacher_id');
        $pair      = $request->query('pair');

        if (!$teacherId || !$pair) return response()->json([]);

        $availability = \App\Models\HR\TeacherAvailability::with('timeSlot')
            ->where('teacher_id', $teacherId)
            ->where('day_of_week', $pair)
            ->first();

        if (!$availability || !$availability->timeSlot) return response()->json([]);

        $slot       = $availability->timeSlot;
        $slotStart  = \Carbon\Carbon::createFromTimeString($slot->start_time);
        $slotEnd    = \Carbon\Carbon::createFromTimeString($slot->end_time);
        $breakSlots = BreakSlot::where('is_active', true)->get();

        $slots   = [];
        $current = $slotStart->copy();

        while ($current->lt($slotEnd)) {
            $timeStr = $current->format('H:i');
            $isBreak = false;
            foreach ($breakSlots as $b) {
                $bStart = \Carbon\Carbon::createFromTimeString($b->start_time);
                $bEnd   = \Carbon\Carbon::createFromTimeString($b->end_time);
                if ($current->gte($bStart) && $current->lt($bEnd)) { $isBreak = true; break; }
            }
            $slots[] = ['start' => $timeStr, 'slot_id' => $slot->time_slot_id, 'is_break' => $isBreak];
            $current->addMinutes(30);
        }

        return response()->json($slots);
    }

    // ────────────────────────────────────────────────────────────
    // AJAX: Occupied slots
    // ────────────────────────────────────────────────────────────
    public function getOccupiedSlots(Request $request)
    {
        $teacherId = $request->query('teacher_id');
        $startDate = $request->query('start_date');
        $endDate   = $request->query('end_date', '2099-12-31');

        if (!$teacherId || !$startDate) return response()->json([]);

        $occupied = \App\Models\Academic\CourseSession::whereHas('courseInstance', fn($q) =>
            $q->where('teacher_id', $teacherId)
        )
        ->whereBetween('session_date', [$startDate, $endDate])
        ->where('status', '!=', 'Cancelled')
        ->pluck('start_time')
        ->map(fn($t) => \Carbon\Carbon::parse($t)->format('H:i'))
        ->unique()->values()->toArray();

        return response()->json($occupied);
    }

    // ────────────────────────────────────────────────────────────
    // AJAX: Check conflicts
    // ────────────────────────────────────────────────────────────
    public function checkConflicts(Request $request)
    {
        $teacherId  = $request->teacher_id;
        $startDate  = $request->start_date;
        $endDate    = $request->end_date ?? '2099-12-31';
        $pairs      = (array) $request->day_of_week;
        $startTime  = $request->start_time;
        $sessionDur = (float) $request->session_duration;

        if (!$teacherId || !$startDate || !$pairs || !$startTime) {
            return response()->json(['conflicts' => []]);
        }

        $dayMap = ['sun_wed' => [0,3], 'sat_tue' => [6,2], 'mon_thu' => [1,4]];
        $allTargetDays = array_merge(...array_map(fn($p) => $dayMap[$p] ?? [], $pairs));
        $newStart      = \Carbon\Carbon::createFromTimeString($startTime);
        $newEnd        = $newStart->copy()->addHours($sessionDur);

        $existingSessions = \App\Models\Academic\CourseSession::with('courseInstance.courseTemplate')
            ->whereHas('courseInstance', fn($q) => $q->where('teacher_id', $teacherId))
            ->whereBetween('session_date', [$startDate, $endDate])
            ->where('status', '!=', 'Cancelled')
            ->get();

        $conflicts = [];
        foreach ($existingSessions as $s) {
            if (!in_array(\Carbon\Carbon::parse($s->session_date)->dayOfWeek, $allTargetDays)) continue;
            $sStart = \Carbon\Carbon::parse($s->start_time);
            $sEnd   = \Carbon\Carbon::parse($s->end_time);
            if ($newStart->lt($sEnd) && $newEnd->gt($sStart)) {
                $conflicts[] = sprintf('%s on %s at %s → %s',
                    $s->courseInstance?->courseTemplate?->name ?? 'Course',
                    \Carbon\Carbon::parse($s->session_date)->format('D d M Y'),
                    $sStart->format('H:i'), $sEnd->format('H:i')
                );
            }
        }

        return response()->json(['conflicts' => array_values(array_unique($conflicts))]);
    }

    // ────────────────────────────────────────────────────────────
    // AJAX: Preview (old modal)
    // ────────────────────────────────────────────────────────────
    public function previewSchedule(Request $request, $id)
    {
        $instance = CourseInstance::findOrFail($id);
        $request->validate([
            'day_of_week' => 'required|in:sun_wed,sat_tue,mon_thu',
            'start_time'  => 'required|date_format:H:i',
        ]);

        return response()->json($this->schedulingService->previewSessions(
            $instance, $request->day_of_week, $request->start_time
        ));
    }

    // ────────────────────────────────────────────────────────────
    // Store Schedule (old modal — kept for backward compat)
    // ────────────────────────────────────────────────────────────
    public function storeSchedule(Request $request, $id)
    {
        $instance = CourseInstance::findOrFail($id);

        $data = $request->validate([
            'day_of_week'  => 'required|in:sun_wed,sat_tue,mon_thu',
            'time_slot_id' => 'required|exists:time_slot,time_slot_id',
            'start_time'   => 'required|date_format:H:i',
        ]);

        $slot = TimeSlot::findOrFail($data['time_slot_id']);

        $this->schedulingService->validateSchedule([
            'start_time'       => $data['start_time'],
            'session_duration' => $instance->session_duration,
            'time_slot'        => $slot,
        ]);

        // Clear old
        \App\Models\Academic\InstanceSchedule::where('course_instance_id', $instance->course_instance_id)->delete();
        \App\Models\Academic\CourseSession::where('course_instance_id', $instance->course_instance_id)->delete();

        $schedule = $this->schedulingService->storeSchedule($instance->course_instance_id, [
            'day_of_week'  => $data['day_of_week'],
            'time_slot_id' => $data['time_slot_id'],
            'start_time'   => $data['start_time'],
        ]);

        $sessionNum = 1;
        $count      = $this->schedulingService->generateSessions($instance, $schedule, $sessionNum);

        $instance->update(['status' => 'Upcoming']);

        return back()->with('success', "Schedule saved — {$count} sessions generated.");
    }

    // ────────────────────────────────────────────────────────────
    // Postpone enrollment
    // ────────────────────────────────────────────────────────────
    public function postponeEnrollment(Request $request, $enrollmentId)
    {
        $enrollment = \App\Models\Enrollment\Enrollment::findOrFail($enrollmentId);

        $request->validate([
            'start_date'           => 'required|date',
            'expected_return_date' => 'required|date|after:start_date',
        ]);

        $scEmployeeId = \App\Models\HR\Employee::where('user_id', auth()->id())->first()?->employee_id;

        \App\Models\Enrollment\Postponement::create([
            'enrollment_id'        => $enrollment->enrollment_id,
            'start_date'           => $request->start_date,
            'expected_return_date' => $request->expected_return_date,
            'status'               => 'Active',
            'reason'               => $request->reason,
            'created_by_cs_id'     => $scEmployeeId,
        ]);

        $enrollment->update(['status' => 'Postponed']);

        return back()->with('success', 'Student postponed successfully.');
    }
}