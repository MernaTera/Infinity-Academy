<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Reports\Report;
use App\Models\Reports\ReportScore;
use App\Models\Enrollment\Enrollment;
use App\Models\HR\Employee;
use App\Models\HR\Teacher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TeacherReportController extends Controller
{
    // Fixed score components
    const COMPONENTS = [
        ['name' => 'Roleplay 1',               'max' => 15],
        ['name' => 'Roleplay 2',               'max' => 15],
        ['name' => 'Writing Task 1',            'max' => 10],
        ['name' => 'Writing Task 2',            'max' => 10],
        ['name' => 'Presentation / Debate',     'max' => 20],
        ['name' => 'Final Exam (MCQ)',           'max' => 20],
        ['name' => 'Final Exam (Writing Task 3)','max' => 10],
    ];

    public function index()
    {
        $employee = Employee::where('user_id', auth()->id())->first();
        $teacher  = Teacher::where('employee_id', $employee?->employee_id)->first();

        if (!$teacher) abort(403, 'Not a teacher account.');

        $enrollments = Enrollment::with([
            'student',
            'courseTemplate',
            'level',
            'sublevel',
            'courseInstance',
            'report',
        ])
        ->whereHas('courseInstance', fn($q) =>
            $q->where('teacher_id', $teacher->teacher_id)
              ->whereIn('status', ['Completed', 'Active'])
        )
        ->get();

        // جيبي الـ reports
        $reports = Report::with([
            'enrollment.student',
            'enrollment.courseTemplate',
            'enrollment.level',
            'enrollment.sublevel',
            'enrollment.courseInstance',
            'reportScores',
        ])
        ->where('teacher_id', $teacher->teacher_id)
        ->latest('updated_at')
        ->get();

        // الـ enrollments اللي مفيهاش report لسه (الـ completed courses)
        $pendingEnrollments = $enrollments->filter(function ($e) use ($teacher) {
            return !$e->report && $e->courseInstance?->status === 'Completed';
        });

        // Deadline warnings — الـ courses اللي انتهت وفات عليها أكتر من 3 أيام ومفيش report
        $overdueEnrollments = $pendingEnrollments->filter(function ($e) {
            $endDate = $e->courseInstance?->end_date;
            return $endDate && \Carbon\Carbon::parse($endDate)->addDays(3)->isPast();
        });

        $stats = [
            'draft'     => $reports->where('status', 'Draft')->count(),
            'submitted' => $reports->where('status', 'Submitted')->count(),
            'approved'  => $reports->where('status', 'Approved')->count(),
            'rejected'  => $reports->where('status', 'Rejected')->count(),
            'sent'      => $reports->where('status', 'Sent')->count(),
            'pending'   => $pendingEnrollments->count(),
            'overdue'   => $overdueEnrollments->count(),
        ];

        return view('teacher.reports.index', compact(
            'reports', 'pendingEnrollments', 'overdueEnrollments', 'stats', 'teacher'
        ));
    }

    public function create(Request $request)
    {
        $employee = Employee::where('user_id', auth()->id())->first();
        $teacher  = Teacher::where('employee_id', $employee?->employee_id)->first();

        $enrollmentId = $request->query('enrollment_id');
        $enrollment   = null;

        if ($enrollmentId) {
            $enrollment = Enrollment::with(['student','courseTemplate','level','sublevel','courseInstance'])
                ->findOrFail($enrollmentId);
        }

        // Check existing report
        if ($enrollment && $enrollment->report) {
            return redirect()->route('teacher.reports.edit', $enrollment->report->report_id);
        }

        // Available completed enrollments without reports
        $availableEnrollments = Enrollment::with(['student','courseTemplate','level','sublevel','courseInstance'])
            ->whereHas('courseInstance', fn($q) =>
                $q->where('teacher_id', $teacher->teacher_id)
                  ->where('status', 'Completed')
            )
            ->whereDoesntHave('report')
            ->get();

        $instance = $enrollment?->courseInstance;

        return view('teacher.reports.create', compact(
            'enrollment', 'availableEnrollments', 'teacher', 'instance' // ← ضيفي instance
        ))->with('components', self::COMPONENTS);
    }

    public function store(Request $request)
    {
        $request->validate([
            'enrollment_id' => 'required|exists:enrollment,enrollment_id',
            'comments'      => 'nullable|string',
            'scores'        => 'required|array',
            'scores.*'      => 'required|numeric|min:0',
            'action'        => 'required|in:save_draft,submit',
        ]);

        $employee = Employee::where('user_id', auth()->id())->first();
        $teacher  = Teacher::where('employee_id', $employee?->employee_id)->first();

        // تأكد إن الـ enrollment مش عنده report
        $existing = Report::where('enrollment_id', $request->enrollment_id)->first();
        if ($existing) {
            return redirect()->route('teacher.reports.edit', $existing->report_id);
        }

        DB::transaction(function () use ($request, $teacher) {
            $totalScore = array_sum($request->scores);

            $report = Report::create([
                'enrollment_id'  => $request->enrollment_id,
                'teacher_id'     => $teacher->teacher_id,
                'total_score'    => $totalScore,
                'status'         => $request->action === 'submit' ? 'Submitted' : 'Draft',
                'submitted_at'   => $request->action === 'submit' ? now() : null,
                'pdf_generated'  => false,
            ]);

            // Store scores
            foreach (self::COMPONENTS as $i => $comp) {
                ReportScore::create([
                    'report_id'      => $report->report_id,
                    'component_name' => $comp['name'],
                    'max_score'      => $comp['max'],
                    'student_score'  => $request->scores[$i] ?? 0,
                ]);
            }

            // Store comments as a score entry with max=0 trick OR add column
            // For now store in a note — if comments column added later use that
            if ($request->filled('comments')) {
                // إضافة comments في rejection_note مؤقتاً لحد ما يتضاف column
                $report->update(['rejection_note' => '__COMMENTS__' . $request->comments]);
            }

            // Notify admin if submitted
            if ($request->action === 'submit') {
                $admins = \App\Models\Auth\User::whereHas('role', fn($q) =>
                    $q->where('role_name', 'Admin')
                )->with('employee')->get();

                foreach ($admins as $admin) {
                    if ($admin->employee) {
                        DB::table('user_notification')->insert([
                            'employee_id'         => $admin->employee->employee_id,
                            'title'               => 'New Report Submitted',
                            'message'             => 'Teacher ' . ($employee?->full_name ?? '') .
                                                     ' submitted a report for student ' .
                                                     (\App\Models\Enrollment\Enrollment::find($request->enrollment_id)?->student?->full_name ?? ''),
                            'related_entity_type' => 'report_submitted',
                            'related_entity_id'   => $report->report_id,
                            'is_read'             => false,
                            'created_at'          => now(),
                            'updated_at'          => now(),
                        ]);
                    }
                }
            }
        });

        return redirect()->route('teacher.reports.index')
            ->with('success', $request->action === 'submit' ? 'Report submitted for admin approval.' : 'Report saved as draft.');
    }

    public function edit($id)
    {
        $employee = Employee::where('user_id', auth()->id())->first();
        $teacher  = Teacher::where('employee_id', $employee?->employee_id)->first();

        $report = Report::with([
            'enrollment.student',
            'enrollment.courseTemplate',
            'enrollment.level',
            'enrollment.sublevel',
            'reportScores',
        ])->where('teacher_id', $teacher->teacher_id)->findOrFail($id);

        if (!in_array($report->status, ['Draft', 'Rejected'])) {
            return redirect()->route('teacher.reports.index')
                ->with('error', 'This report cannot be edited in its current state.');
        }

        return view('teacher.reports.edit', compact('report', 'teacher'))
            ->with('components', self::COMPONENTS);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'comments' => 'nullable|string',
            'scores'   => 'required|array',
            'scores.*' => 'required|numeric|min:0',
            'action'   => 'required|in:save_draft,submit',
        ]);

        $employee = Employee::where('user_id', auth()->id())->first();
        $teacher  = Teacher::where('employee_id', $employee?->employee_id)->first();

        $report = Report::where('teacher_id', $teacher->teacher_id)->findOrFail($id);

        if (!in_array($report->status, ['Draft', 'Rejected'])) {
            return back()->with('error', 'Cannot edit this report.');
        }

        DB::transaction(function () use ($request, $report, $employee) {
            $totalScore = array_sum($request->scores);

            $report->update([
                'total_score'  => $totalScore,
                'status'       => $request->action === 'submit' ? 'Submitted' : 'Draft',
                'submitted_at' => $request->action === 'submit' ? now() : $report->submitted_at,
                'rejection_note' => $request->filled('comments')
                    ? '__COMMENTS__' . $request->comments
                    : ($report->rejection_note && str_starts_with($report->rejection_note, '__COMMENTS__')
                        ? $report->rejection_note
                        : null),
            ]);

            // Update scores
            $report->reportScores()->delete();
            foreach (self::COMPONENTS as $i => $comp) {
                ReportScore::create([
                    'report_id'      => $report->report_id,
                    'component_name' => $comp['name'],
                    'max_score'      => $comp['max'],
                    'student_score'  => $request->scores[$i] ?? 0,
                ]);
            }

            if ($request->action === 'submit') {
                $admins = \App\Models\Auth\User::whereHas('role', fn($q) =>
                    $q->where('role_name', 'Admin')
                )->with('employee')->get();

                foreach ($admins as $admin) {
                    if ($admin->employee) {
                        \Illuminate\Support\Facades\DB::table('user_notification')->insert([
                            'employee_id'         => $admin->employee->employee_id,
                            'title'               => 'Report Resubmitted',
                            'message'             => 'Teacher ' . ($employee?->full_name ?? '') . ' resubmitted a report.',
                            'related_entity_type' => 'report_submitted',
                            'related_entity_id'   => $report->report_id,
                            'is_read'             => false,
                            'created_at'          => now(),
                            'updated_at'          => now(),
                        ]);
                    }
                }
            }
        });

        return redirect()->route('teacher.reports.index')
            ->with('success', $request->action === 'submit' ? 'Report resubmitted.' : 'Draft saved.');
    }

    public function markSent($id)
    {
        $employee = Employee::where('user_id', auth()->id())->first();
        $teacher  = Teacher::where('employee_id', $employee?->employee_id)->first();

        $report = Report::where('teacher_id', $teacher->teacher_id)
            ->where('status', 'Approved')
            ->findOrFail($id);

        $report->markSent();

        return back()->with('success', 'Report marked as sent to student.');
    }
}