<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Finance\InstallmentApprovalLog;
use App\Models\Enrollment\Enrollment;
use App\Models\Finance\InstallmentSchedule;
use App\Models\HR\Employee;
use Illuminate\Http\Request;

class InstallmentApprovalController extends Controller
{
    public function index()
    {
        $pending = InstallmentApprovalLog::with([
            'enrollment.student',
            'enrollment.courseInstance.courseTemplate',
            'enrollment.courseInstance.patch',
            'enrollment.paymentPlan',
            'createdByCs',
        ])->where('status', 'Pending')
          ->orderByDesc('created_at')
          ->get();

        $history = InstallmentApprovalLog::with([
            'enrollment.student',
            'approvedBy',
        ])->whereIn('status', ['Approved', 'Rejected'])
          ->orderByDesc('created_at')
          ->limit(30)
          ->get();

        $stats = [
            'pending'  => $pending->count(),
            'approved' => InstallmentApprovalLog::where('status', 'Approved')->count(),
            'rejected' => InstallmentApprovalLog::where('status', 'Rejected')->count(),
        ];

        return view('admin.installments.index', compact('pending', 'history', 'stats'));
    }

    public function approve($id)
    {
        $log = InstallmentApprovalLog::with('enrollment')->findOrFail($id);

        $adminId = Employee::where('user_id', auth()->id())->first()?->employee_id;

        $log->update([
            'status'      => 'Approved',
            'approved_by' => $adminId,
            'approved_at' => now(),
        ]);

        // Update enrollment payment plan
        $log->enrollment->update([
            'payment_plan_id' => $log->new_plan_id,
        ]);

        return back()->with('success', 'Installment plan approved.');
    }

    public function reject(Request $request, $id)
    {
        $log = InstallmentApprovalLog::findOrFail($id);

        $adminId = Employee::where('user_id', auth()->id())->first()?->employee_id;

        $log->update([
            'status'          => 'Rejected',
            'approved_by'     => $adminId,
            'approved_at'     => now(),
            'rejection_note'  => $request->reason,
        ]);

        return back()->with('success', 'Request rejected.');
    }
}