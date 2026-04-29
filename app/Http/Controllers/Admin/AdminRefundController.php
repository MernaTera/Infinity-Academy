<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Finance\RefundRequest;
use App\Models\Finance\FinancialTransaction;
use App\Models\Enrollment\Enrollment;
use App\Models\HR\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminRefundController extends Controller
{
    public function index()
    {
        $requests = RefundRequest::with([
            'enrollment.student',
            'enrollment.courseTemplate',
            'enrollment.level',
            'enrollment.financialTransactions',
            'requestedBy',
            'approvedBy',
        ])
        ->latest()
        ->get();

        $stats = [
            'pending'   => $requests->where('status', 'Pending')->count(),
            'approved'  => $requests->where('status', 'Approved')->count(),
            'processed' => $requests->where('status', 'Processed')->count(),
            'rejected'  => $requests->where('status', 'Rejected')->count(),
        ];

        return view('admin.refunds.index', compact('requests', 'stats'));
    }

    public function approve(Request $request, $id)
    {
        $refund = RefundRequest::with([
            'enrollment.financialTransactions',
            'enrollment',
        ])->findOrFail($id);

        if ($refund->status !== 'Pending') {
            return back()->with('error', 'Request is not pending.');
        }

        $adminEmployee = Employee::where('user_id', auth()->id())->first();
        $enrollment    = $refund->enrollment;

        DB::transaction(function () use ($refund, $enrollment, $adminEmployee) {

            // 1) Create Refund transaction
            $transaction = FinancialTransaction::create([
                'enrollment_id'          => $enrollment->enrollment_id,
                'patch_id'               => $enrollment->patch_id,
                'branch_id' => $enrollment->branch_id 
                            ?? $enrollment->courseInstance?->branch_id 
                            ?? \App\Models\Core\Branch::first()?->branch_id,
                'transaction_type'       => 'Refund',
                'transaction_category'   => 'Course',
                'amount'                 => $refund->amount,
                'payment_method'         => 'Cash',
                'notes'                  => 'Full deposit refund — approved by admin. Reason: ' . $refund->reason,
                'created_by_employee_id' => $adminEmployee->employee_id,
            ]);

            // 2) Mark refund request as Processed
            $refund->update([
                'status'                   => 'Approved',
                'approved_by_admin_id'     => $adminEmployee->employee_id,
                'approved_at'              => now(),
                'processed_transaction_id' => $transaction->transaction_id,
            ]);

            // 3) Cancel enrollment
            $enrollment->update(['status' => 'Cancelled']);
        });

        return back()->with('success', 'Refund approved and processed. Enrollment cancelled.');
    }

    public function reject(Request $request, $id)
    {
        $request->validate(['reason' => 'required|string|min:5']);

        $refund = RefundRequest::findOrFail($id);

        if ($refund->status !== 'Pending') {
            return back()->with('error', 'Request is not pending.');
        }

        $adminEmployee = Employee::where('user_id', auth()->id())->first();

        $refund->update([
            'status'               => 'Rejected',
            'approved_by_admin_id' => $adminEmployee->employee_id,
            'approved_at'          => now(),
            'rejection_note'       => $request->reason,
        ]);

        return back()->with('success', 'Refund request rejected.');
    }
}