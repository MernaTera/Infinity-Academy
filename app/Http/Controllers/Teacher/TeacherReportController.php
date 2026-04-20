<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Academic\CourseInstance;
use App\Models\Enrollment\Enrollment;
use App\Models\HR\Teacher;
use App\Models\HR\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TeacherReportController extends Controller
{
    private function getTeacher()
    {
        return Teacher::where('employee_id',
            Employee::where('user_id', auth()->id())->first()?->employee_id
        )->first();
    }

    public function index()
    {
        $teacher = $this->getTeacher();

        // Completed courses with report status per enrollment
        $completedInstances = CourseInstance::with([
            'courseTemplate', 'level', 'patch',
            'enrollments.student',
            'enrollments.report',
        ])
        ->where('teacher_id', $teacher->teacher_id)
        ->where('status', 'Completed')
        ->orderByDesc('end_date')
        ->get();

        // Stats
        $stats = [
            'pending'   => 0,
            'submitted' => 0,
            'approved'  => 0,
            'rejected'  => 0,
        ];

        foreach ($completedInstances as $inst) {
            foreach ($inst->enrollments as $enr) {
                $status = $enr->report?->status ?? 'Draft';
                match($status) {
                    'Draft'     => $stats['pending']++,
                    'Submitted' => $stats['submitted']++,
                    'Approved'  => $stats['approved']++,
                    'Rejected'  => $stats['rejected']++,
                    default     => null,
                };
            }
        }

        return view('teacher.reports.index', compact('completedInstances', 'stats'));
    }

    public function create($instanceId)
    {
        $teacher  = $this->getTeacher();
        $instance = CourseInstance::with([
            'courseTemplate', 'level',
            'enrollments.student',
            'enrollments.report.scores',
        ])
        ->where('teacher_id', $teacher->teacher_id)
        ->where('status', 'Completed')
        ->findOrFail($instanceId);

        return view('teacher.reports.create', compact('instance'));
    }

    public function store(Request $request)
    {
        $teacher = $this->getTeacher();

        $request->validate([
            'enrollment_id'  => 'required|exists:enrollment,enrollment_id',
            'roleplay_1'     => 'required|numeric|min:0|max:15',
            'roleplay_2'     => 'required|numeric|min:0|max:15',
            'writing_1'      => 'required|numeric|min:0|max:10',
            'writing_2'      => 'required|numeric|min:0|max:10',
            'presentation'   => 'required|numeric|min:0|max:20',
            'mcq'            => 'required|numeric|min:0|max:20',
            'writing_final'  => 'required|numeric|min:0|max:10',
            'comments'       => 'nullable|string',
        ]);

        $enrollment = Enrollment::findOrFail($request->enrollment_id);

        $total = $request->roleplay_1 + $request->roleplay_2
               + $request->writing_1 + $request->writing_2
               + $request->presentation + $request->mcq
               + $request->writing_final;

        DB::transaction(function () use ($request, $enrollment, $teacher, $total) {
            $report = \App\Models\Academic\Report::updateOrCreate(
                ['enrollment_id' => $enrollment->enrollment_id],
                [
                    'teacher_id'   => $teacher->employee_id,
                    'total_score'  => $total,
                    'status'       => 'Submitted',
                    'comments'     => $request->comments,
                    'submitted_at' => now(),
                ]
            );

            // Save scores
            $components = [
                ['Roleplay 1',        15, $request->roleplay_1],
                ['Roleplay 2',        15, $request->roleplay_2],
                ['Writing Task 1',    10, $request->writing_1],
                ['Writing Task 2',    10, $request->writing_2],
                ['Presentation',      20, $request->presentation],
                ['Final MCQ',         20, $request->mcq],
                ['Final Writing Task',10, $request->writing_final],
            ];

            \App\Models\Academic\ReportScore::where('report_id', $report->report_id)->delete();

            foreach ($components as [$name, $max, $score]) {
                \App\Models\Academic\ReportScore::create([
                    'report_id'       => $report->report_id,
                    'component_name'  => $name,
                    'max_score'       => $max,
                    'student_score'   => $score,
                ]);
            }
        });

        return redirect()->route('teacher.reports.index')
            ->with('success', 'Report submitted successfully.');
    }

    public function edit($id)
    {
        $teacher = $this->getTeacher();
        $report  = \App\Models\Academic\Report::with([
            'enrollment.student',
            'enrollment.courseInstance.courseTemplate',
            'scores',
        ])->where('teacher_id', $teacher->employee_id)
          ->whereIn('status', ['Draft', 'Rejected'])
          ->findOrFail($id);

        return view('teacher.reports.edit', compact('report'));
    }

    public function update(Request $request, $id)
    {
        $teacher = $this->getTeacher();
        $report  = \App\Models\Academic\Report::where('teacher_id', $teacher->employee_id)
            ->findOrFail($id);

        $request->validate([
            'roleplay_1'    => 'required|numeric|min:0|max:15',
            'roleplay_2'    => 'required|numeric|min:0|max:15',
            'writing_1'     => 'required|numeric|min:0|max:10',
            'writing_2'     => 'required|numeric|min:0|max:10',
            'presentation'  => 'required|numeric|min:0|max:20',
            'mcq'           => 'required|numeric|min:0|max:20',
            'writing_final' => 'required|numeric|min:0|max:10',
            'comments'      => 'nullable|string',
        ]);

        $total = $request->roleplay_1 + $request->roleplay_2
               + $request->writing_1 + $request->writing_2
               + $request->presentation + $request->mcq
               + $request->writing_final;

        DB::transaction(function () use ($request, $report, $total) {
            $report->update([
                'total_score'  => $total,
                'status'       => 'Submitted',
                'comments'     => $request->comments,
                'submitted_at' => now(),
                'rejection_note' => null,
            ]);

            $components = [
                ['Roleplay 1',        15, $request->roleplay_1],
                ['Roleplay 2',        15, $request->roleplay_2],
                ['Writing Task 1',    10, $request->writing_1],
                ['Writing Task 2',    10, $request->writing_2],
                ['Presentation',      20, $request->presentation],
                ['Final MCQ',         20, $request->mcq],
                ['Final Writing Task',10, $request->writing_final],
            ];

            \App\Models\Academic\ReportScore::where('report_id', $report->report_id)->delete();
            foreach ($components as [$name, $max, $score]) {
                \App\Models\Academic\ReportScore::create([
                    'report_id'      => $report->report_id,
                    'component_name' => $name,
                    'max_score'      => $max,
                    'student_score'  => $score,
                ]);
            }
        });

        return redirect()->route('teacher.reports.index')
            ->with('success', 'Report resubmitted successfully.');
    }
}