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
        $report = Report::with(['teacher.employee', 'enrollment.student'])->findOrFail($id);

        if ($report->status !== 'Submitted') {
            return back()->with('error', 'Report is not in submitted state.');
        }

        $adminEmployee = Employee::where('user_id', auth()->id())->first();
        $report->approve($adminEmployee?->employee_id);

        $teacherEmployeeId = $report->teacher?->employee_id;
        $studentName       = $report->enrollment?->student?->full_name ?? '—';

        if ($teacherEmployeeId) {
            \App\Services\NotificationService::send(
                (int) $teacherEmployeeId,
                'Report Approved',
                "Your report for {$studentName} has been approved. You can now send it to the student.",
                'report_approved',
                $report->report_id
            );
        }

        return redirect()->route('admin.reports.index')
            ->with('success', 'Report approved successfully.');
    }

    public function reject(Request $request, $id)
    {
        $request->validate(['reason' => 'required|string|min:3']);

        $report = Report::with(['teacher.employee', 'enrollment.student'])->findOrFail($id);

        if ($report->status !== 'Submitted') {
            return back()->with('error', 'Report is not in submitted state.');
        }

        $adminEmployee = Employee::where('user_id', auth()->id())->first();
        $report->reject($adminEmployee?->employee_id, $request->reason);

        $teacherEmployeeId = $report->teacher?->employee_id;
        $studentName       = $report->enrollment?->student?->full_name ?? '—';

        if ($teacherEmployeeId) {
            \App\Services\NotificationService::send(
                (int) $teacherEmployeeId,
                'Report Rejected',
                "Your report for {$studentName} was rejected. Reason: {$request->reason}",
                'report_rejected',
                $report->report_id
            );
        }

        return redirect()->route('admin.reports.index')
            ->with('success', 'Report rejected. Teacher has been notified.');
    }
}