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

class CourseInstanceController extends Controller
{
    public function index()
    {
        $instances = CourseInstance::with([
            'courseTemplate','level','sublevel','teacher','patch','enrollments'
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
            'enrollments.student.phones'
        ])->findOrFail($id);

        return view('student-care.course-instances.show', compact('instance'));
    }

    protected $schedulingService;

    public function __construct(SchedulingService $schedulingService)
    {
        $this->schedulingService = $schedulingService;
    }

    public function getScheduleData($id)
    {
        $instance = CourseInstance::findOrFail($id);

        $availability = $this->schedulingService
            ->getTeacherAvailability($instance->teacher_id);

        return response()->json($availability);
    }

    public function storeSchedule(Request $request, $id)
    {
        $instance = CourseInstance::findOrFail($id);

        $data = $request->validate([
            'day_of_week' => 'required',
            'time_slot_id' => 'required|exists:time_slot,time_slot_id',
            'start_time' => 'required'
        ]);

        $slot = TimeSlot::find($data['time_slot_id']);

        $this->schedulingService->validateSchedule([
            'start_time' => $data['start_time'],
            'time_slot' => $slot
        ]);

        $this->schedulingService->storeSchedule($id, $data);

        return back()->with('success', 'Schedule saved successfully');
    }
}
