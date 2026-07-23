<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HR\Teacher;
use App\Models\HR\Employee;
use App\Models\HR\TeacherContract;
use App\Models\HR\TeacherAvailability;
use App\Models\Academic\Patch;
use App\Models\Academic\CourseInstance;
use App\Models\Reports\Report;
use Carbon\Carbon;

class TeacherProfileController extends Controller
{
    public function show($id)
    {
        $teacher = Teacher::with([
            'employee.user',
            'employee.branch',
            'englishLevel',
        ])->findOrFail($id);

        $employee = $teacher->employee;

        $currentPatch = Patch::where('status', 'Active')->latest('start_date')->first();

        $contract = TeacherContract::with('contractType')
            ->where('teacher_id', $teacher->teacher_id)
            ->where('is_active', true)
            ->when($currentPatch, fn($q) => $q->where('patch_id', $currentPatch->patch_id))
            ->latest('created_at')
            ->first();

        $allContracts = TeacherContract::with('contractType', 'patch')
            ->where('teacher_id', $teacher->teacher_id)
            ->orderByDesc('created_at')
            ->get();

        $availability = TeacherAvailability::with('timeSlot')
            ->where('teacher_id', $teacher->teacher_id)
            ->get();
        $availByPair = $availability->groupBy('day_of_week');
        $pairLabels = [
            'sat_tue' => 'Sat & Tue',
            'sun_wed' => 'Sun & Wed',
            'mon_thu' => 'Mon & Thu',
        ];

        $allInstances = CourseInstance::with([
            'courseTemplate', 'level', 'sublevel', 'patch',
            'enrollments.student', 'enrollments.report',
            'sessions',
        ])
        ->where('teacher_id', $teacher->teacher_id)
        ->orderByDesc('start_date')
        ->get();

        $currentInstances = $currentPatch
            ? $allInstances->where('patch_id', $currentPatch->patch_id)
            : collect();

        $activeInstances    = $currentInstances->where('status', 'Active');
        $upcomingInstances  = $currentInstances->where('status', 'Upcoming');
        $completedInstances = $currentInstances->where('status', 'Completed');

        $sessionsThisPatch = $currentInstances->sum(fn($i) => $i->sessions->count());
        $completedSessions = $currentInstances->sum(
            fn($i) => $i->sessions->where('status', 'Completed')->count()
        );

        $studentsThisPatch = $currentInstances
            ->flatMap(fn($i) => $i->enrollments->pluck('student_id'))
            ->unique()
            ->count();

        $maxAllowed = $contract?->contractType?->max_sessions_allowed ?? 0;
        $isOverload = $maxAllowed > 0 && $sessionsThisPatch > $maxAllowed;

        $reportStats = [
            'pending'   => 0,
            'overdue'   => 0,
            'submitted' => 0,
            'approved'  => 0,
            'rejected'  => 0,
            'sent'      => 0,
        ];

        foreach ($allInstances->where('status', 'Completed') as $inst) {
            $deadline = $inst->end_date
                ? Carbon::parse($inst->end_date)->addDays(3)
                : null;
            $isPastDeadline = $deadline && $deadline->isPast();

            foreach ($inst->enrollments as $enr) {
                $status = $enr->report?->status;
                if (!$status) {
                    $reportStats['pending']++;
                    if ($isPastDeadline) $reportStats['overdue']++;
                    continue;
                }
                match($status) {
                    'Draft'     => $reportStats['pending']++,
                    'Submitted' => $reportStats['submitted']++,
                    'Approved'  => $reportStats['approved']++,
                    'Rejected'  => $reportStats['rejected']++,
                    'Sent'      => $reportStats['sent']++,
                    default     => null,
                };
            }
        }

        $totalCoursesAllTime  = $allInstances->count();
        $totalSessionsAllTime = $allInstances->sum(fn($i) => $i->sessions->count());
        $totalStudentsAllTime = $allInstances
            ->flatMap(fn($i) => $i->enrollments->pluck('student_id'))
            ->unique()
            ->count();

        return view('admin.teachers.show', compact(
            'teacher', 'employee', 'currentPatch',
            'contract', 'allContracts',
            'availByPair', 'pairLabels',
            'currentInstances', 'activeInstances', 'upcomingInstances', 'completedInstances',
            'sessionsThisPatch', 'completedSessions', 'studentsThisPatch',
            'maxAllowed', 'isOverload',
            'reportStats',
            'totalCoursesAllTime', 'totalSessionsAllTime', 'totalStudentsAllTime',
            'allInstances'
        ));
    }
}