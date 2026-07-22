<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Reports\Report;
use App\Models\HR\Employee;
use App\Models\HR\Teacher;
use Illuminate\Http\Request;

class AdminReportController extends Controller
{
    public function index(Request $request)
    {
        $filterStatus  = $request->query('status', 'all');
        $filterTeacher = $request->query('teacher_id', 'all');

        $completedInstances = \App\Models\Academic\CourseInstance::with([
            'courseTemplate', 'level', 'sublevel', 'patch',
            'teacher.employee',
            'sessions',
            'enrollments.student.phones',
            'enrollments.report.approvedBy',
            'enrollments.attendances',
        ])
        ->where('status', 'Completed')
        ->when($filterTeacher !== 'all', fn($q) => $q->where('teacher_id', $filterTeacher))
        ->orderBy('end_date', 'asc')
        ->get();

        $stats = [
            'pending'   => 0,
            'draft'     => 0,
            'submitted' => 0,
            'approved'  => 0,
            'rejected'  => 0,
            'sent'      => 0,
            'overdue'   => 0,
        ];

        foreach ($completedInstances as $inst) {
            $deadline = $inst->end_date
                ? \Carbon\Carbon::parse($inst->end_date)->addDays(3)
                : null;
            $isPastDeadline = $deadline && $deadline->isPast();

            foreach ($inst->enrollments as $enr) {
                $status = $enr->report?->status;
                if (!$status) {
                    $stats['pending']++;
                    if ($isPastDeadline) $stats['overdue']++;
                    continue;
                }
                match($status) {
                    'Draft'     => $stats['draft']++,
                    'Submitted' => $stats['submitted']++,
                    'Approved'  => $stats['approved']++,
                    'Rejected'  => $stats['rejected']++,
                    'Sent'      => $stats['sent']++,
                    default     => null,
                };
            }
        }

        $teachers = Teacher::with('employee')
            ->whereHas('courseInstances', fn($q) => $q->where('status', 'Completed'))
            ->get();

        return view('admin.reports.index', compact(
            'completedInstances', 'stats', 'filterStatus', 'filterTeacher', 'teachers'
        ));
    }

    public function show($id)
    {
        $report = Report::with([
            'enrollment.student',
            'enrollment.courseTemplate',
            'enrollment.level',
            'enrollment.sublevel',
            'enrollment.courseInstance',
            'teacher.employee',
            'reportScores',
            'approvedBy',
        ])->findOrFail($id);

        return view('admin.reports.show', compact('report'));
    }

    public function approve(Request $request, $id)
    {
        $report = Report::findOrFail($id);

        if ($report->status !== 'Submitted') {
            return back()->with('error', 'Report is not in submitted state.');
        }

        $adminEmployee = Employee::where('user_id', auth()->id())->first();
        $report->approve($adminEmployee?->employee_id);

        \Illuminate\Support\Facades\DB::table('user_notification')->insert([
            'employee_id'         => $report->teacher?->employee_id,
            'title'               => 'Report Approved',
            'message'             => 'Your report for student ' .
                                     ($report->enrollment?->student?->full_name ?? '—') .
                                     ' has been approved. You can now send it to the student.',
            'related_entity_type' => 'report_approved',
            'related_entity_id'   => $report->report_id,
            'is_read'             => false,
            'created_at'          => now(),
            'updated_at'          => now(),
        ]);

        return back()->with('success', 'Report approved successfully.');
    }

    public function reject(Request $request, $id)
    {
        $request->validate(['reason' => 'required|string|min:5']);

        $report = Report::findOrFail($id);

        if ($report->status !== 'Submitted') {
            return back()->with('error', 'Report is not in submitted state.');
        }

        $adminEmployee = Employee::where('user_id', auth()->id())->first();
        $report->reject($adminEmployee?->employee_id, $request->reason);

        \Illuminate\Support\Facades\DB::table('user_notification')->insert([
            'employee_id'         => $report->teacher?->employee_id,
            'title'               => 'Report Rejected',
            'message'             => 'Your report for student ' .
                                     ($report->enrollment?->student?->full_name ?? '—') .
                                     ' was rejected. Reason: ' . $request->reason,
            'related_entity_type' => 'report_rejected',
            'related_entity_id'   => $report->report_id,
            'is_read'             => false,
            'created_at'          => now(),
            'updated_at'          => now(),
        ]);

        return back()->with('success', 'Report rejected.');
    }
}