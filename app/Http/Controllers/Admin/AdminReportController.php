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
        $filterStatus  = $request->query('status', 'Submitted');
        $filterTeacher = $request->query('teacher_id', 'all');

        $reports = Report::with([
            'enrollment.student',
            'enrollment.courseTemplate',
            'enrollment.level',
            'enrollment.sublevel',
            'enrollment.courseInstance',
            'teacher.employee',
            'reportScores',
            'approvedBy',
        ])
        ->when($filterStatus !== 'all', fn($q) => $q->where('status', $filterStatus))
        ->when($filterTeacher !== 'all', fn($q) => $q->where('teacher_id', $filterTeacher))
        ->latest('updated_at')
        ->get();

        // Teachers who have reports
        $teachers = Teacher::with('employee')
            ->whereHas('reports')
            ->get();

        $stats = [
            'submitted' => Report::where('status', 'Submitted')->count(),
            'approved'  => Report::where('status', 'Approved')->count(),
            'rejected'  => Report::where('status', 'Rejected')->count(),
            'sent'      => Report::where('status', 'Sent')->count(),
        ];

        // Group by teacher → course
        $grouped = $reports->groupBy(fn($r) => $r->teacher_id);

        return view('admin.reports.index', compact(
            'reports', 'stats', 'filterStatus', 'filterTeacher', 'teachers', 'grouped'
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