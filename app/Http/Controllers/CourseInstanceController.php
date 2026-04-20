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
    public function index()
    {
        $instances = CourseInstance::with([
            'courseTemplate','level','sublevel','teacher','patch','enrollments','sessions','instanceSchedules',
        ])->latest()->paginate(10);

        $templates = \App\Models\Academic\CourseTemplate::all();
        $teachers = Teacher::with('employee')->get();
        $patches = Patch::whereIn('status', ['Active', 'Upcoming'])->get();
        $branches  = \App\Models\Core\Branch::all();
        $rooms = Room::all();
        return view('student-care.course-instances.index', compact(
            'instances','templates','teachers','patches','branches','rooms'
        ));
    }

    public function storeInstance(Request $request)
    {
        $data = $request->validate([
            'course_template_id' => 'required|exists:course_template,course_template_id',
            'level_id' => 'nullable|exists:level,level_id',
            'sublevel_id' => 'nullable|exists:sublevel,sublevel_id',

            'patch_id' => 'required|exists:patch,patch_id',
            'teacher_id' => 'required|exists:teacher,teacher_id',
            'branch_id' => 'required|exists:branch,branch_id',

            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',

            'capacity' => 'required|integer|min:1',

            'delivery_mood' => 'required|in:Online,Offline',
            'type' => 'required|in:Group,Private',
            'room_id' => 'nullable|exists:room,room_id',

            'total_hours' => 'required|numeric',
            'session_duration' => 'required|numeric',
        ]);

        CourseInstance::create([
            ...$data,
            'status' => 'Upcoming',
            'created_by_employee_id' => auth()->user()->employees->first()->employee_id ?? null,
        ]);

        return back()->with('success', 'Instance created successfully');
    }

    public function getTeachersByCourse($courseId)
    {
        $course = \App\Models\Academic\CourseTemplate::find($courseId);

        
        if (!$course || !$course->english_level_id) {
            return response()->json([]);
        }

        $requiredLevel = $course->english_level_id;

        $teachers = \App\Models\HR\Teacher::where('english_level_id', '>=', $requiredLevel)
            ->with('employee')
            ->get();

        return response()->json($teachers);
    }

    public function getTeachersByLevel($englishLevelId)
    {
        $teachers = Teacher::where('english_level_id', '>=', $englishLevelId)
            ->where('is_active', true)
            ->with(['employee', 'englishLevel'])
            ->get();

        return response()->json($teachers);
    }

    public function show($id)
    {
        $instance = CourseInstance::with([
            'courseTemplate',
            'level',
            'sublevel',
            'teacher.employee',
            'branch',
            'patch',
            'room',
            'enrollments.student.phones',
            'sessions',              
            'instanceSchedules.timeSlot',
            'enrollments.activePostponement', 
        ])->findOrFail($id);

        return view('student-care.course-instances.show', compact('instance'));
    }

    protected $schedulingService;

    public function __construct(SchedulingService $schedulingService)
    {
        $this->schedulingService = $schedulingService;
    }
    /*
    |------------------------------------------------------------------
    | Schedule Data — للـ AJAX (teacher availability)
    |------------------------------------------------------------------
    */
    public function getScheduleData($id)
    {
        $instance = CourseInstance::with('teacher')->findOrFail($id);

        $pairs = $this->schedulingService
            ->getTeacherAvailablePairs($instance->teacher_id);

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

    public function validateSchedule(array $data): void
    {
        $startTime  = Carbon::createFromTimeString($data['start_time']);
        $slotStart  = Carbon::createFromTimeString($data['time_slot']->start_time);
        $slotEnd    = Carbon::createFromTimeString($data['time_slot']->end_time);
        $sessionEnd = $startTime->copy()->addHours((float) $data['session_duration']);

        // لازم جوا الـ slot
        if ($startTime->lt($slotStart) || $sessionEnd->gt($slotEnd)) {
            throw new \Exception(
                "Session ({$startTime->format('H:i')} → {$sessionEnd->format('H:i')}) " .
                "must be within slot ({$slotStart->format('H:i')} → {$slotEnd->format('H:i')})"
            );
        }

        // Break slots — بس لو في data صح في الـ DB
        $breaks = BreakSlot::all();
        foreach ($breaks as $break) {
            // تأكد إن الـ break times صح
            if (!$break->start_time || !$break->end_time) continue;

            $breakStart = Carbon::createFromTimeString($break->start_time);
            $breakEnd   = Carbon::createFromTimeString($break->end_time);

            // لو السيشن بتعدي في وقت الـ break
            if ($startTime->lt($breakEnd) && $sessionEnd->gt($breakStart)) {
                throw new \Exception(
                    "Session overlaps with break ({$breakStart->format('H:i')} → {$breakEnd->format('H:i')}). " .
                    "Please choose a different start time."
                );
            }
        }
    }

    /*
    |------------------------------------------------------------------
    | Preview Sessions — AJAX قبل الحفظ
    |------------------------------------------------------------------
    */
    public function previewSchedule(Request $request, $id)
    {
        $instance = CourseInstance::findOrFail($id);

        $request->validate([
            'day_of_week' => 'required|in:sun_wed,sat_tue,mon_thu',
            'start_time'  => 'required|date_format:H:i',
        ]);

        $preview = $this->schedulingService->previewSessions(
            $instance,
            $request->day_of_week,
            $request->start_time
        );

        return response()->json($preview);
    }

    /*
    |------------------------------------------------------------------
    | Store Schedule + Generate Sessions
    |------------------------------------------------------------------
    */
    public function storeSchedule(Request $request, $id)
    {
        $instance = CourseInstance::findOrFail($id);

        $data = $request->validate([
            'day_of_week'  => 'required|in:sun_wed,sat_tue,mon_thu',
            'time_slot_id' => 'required|exists:time_slot,time_slot_id',
            'start_time'   => 'required|date_format:H:i',
        ]);

        $slot = \App\Models\Academic\TimeSlot::findOrFail($data['time_slot_id']);

        // Validate
        $this->schedulingService->validateSchedule([
            'start_time'       => $data['start_time'],
            'session_duration' => $instance->session_duration,
            'time_slot'        => $slot,
        ]);

        // Store schedule
        $schedule = $this->schedulingService->storeSchedule($id, $data);

        // Generate sessions
        $count = $this->schedulingService->generateSessions($instance, $schedule);

        // Update instance status
        $instance->update(['status' => 'Upcoming']);

        return back()->with('success', "Schedule saved — {$count} sessions generated successfully.");
    }

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
