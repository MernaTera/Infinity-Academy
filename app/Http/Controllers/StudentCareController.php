<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\StudentCareService;
use App\Models\Enrollment\WaitingList;
use App\Models\Enrollment\Enrollment;
use App\Models\Academic\CourseInstance;
use App\Models\Academic\CourseTemplate;
use App\Models\HR\Teacher;
use App\Models\Academic\Patch;
use App\Models\Core\Branch;

class StudentCareController extends Controller
{
    protected $service;

    public function __construct(StudentCareService $service)
    {
        $this->service = $service;
    }

    public function waitingList()
    {
        $waiting = WaitingList::with([
            'enrollment.student',
            'enrollment.courseTemplate',
            'enrollment.level'
        ])->get();

        $instances = CourseInstance::with([
            'courseTemplate',
            'teacher',
            'enrollments'
        ])
        ->where('status', 'Upcoming')
        ->get();

        return view('student-care.waiting-list', compact('waiting', 'instances'));
    }

public function assign(Request $request)
{
    $request->validate([
        'waiting_id' => 'required|exists:waiting_list,waiting_id',
        'course_instance_id' => 'required|exists:course_instance,course_instance_id',
    ]);
    $waiting = WaitingList::with('enrollment')->findOrFail($request->waiting_id);

    $instances = CourseInstance::with(['courseTemplate','teacher','enrollments'])
        ->where('status', 'Upcoming')
        ->where('course_template_id', $waiting->enrollment->course_template_id)
        ->get();
    $instance = CourseInstance::with('enrollments')->findOrFail($request->course_instance_id);

    if ($instance->isFull()) {
        return back()->with('error', 'Instance is full');
    }

    $waiting->enrollment->update([
        'course_instance_id' => $instance->course_instance_id,
        'status' => 'Active'
    ]);

    $waiting->update([
        'status' => 'Assigned'
    ]);

    return back()->with('success', 'Student assigned successfully');
}


}
