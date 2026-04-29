<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Finance\RefundRequest;
use App\Models\Finance\FinancialTransaction;
use App\Models\Enrollment\Enrollment;
use App\Models\HR\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class RefundController extends Controller
{
    /*
    |------------------------------------------------------------------
    | CS — Refund requests index
    |------------------------------------------------------------------
    */
    public function index()
    {
        $employeeId = Employee::where('user_id', auth()->id())->value('employee_id');

        // Enrollments created by this CS that have a deposit paid within 3 days
        $eligibleEnrollments = Enrollment::with([
            'student',
            'courseTemplate',
            'level',
            'financialTransactions' => fn($q) => $q->where('transaction_type', 'Payment')
                                                    ->where('transaction_category', 'Course')
                                                    ->orderBy('created_at'),
            'refundRequests',
        ])
        ->where('created_by_cs_id', $employeeId)
        ->whereIn('status', ['Active', 'Pending_Approval', 'Waiting'])
        ->get()
        ->filter(function ($enrollment) {
            $deposit = $enrollment->financialTransactions->first();
            if (!$deposit) return false;
            // Within 3 days of payment
            return $deposit->created_at->diffInDays(now()) <= 3;
        });

        // My pending refund requests
        $myRequests = RefundRequest::with([
            'enrollment.student',
            'enrollment.courseTemplate',
        ])
        ->where('requested_by', $employeeId)
        ->latest()
        ->get();

        $stats = [
            'eligible' => $eligibleEnrollments->count(),
            'pending'  => $myRequests->where('status', 'Pending')->count(),
            'approved' => $myRequests->where('status', 'Approved')->count(),
            'processed'=> $myRequests->where('status', 'Processed')->count(),
        ];

        return view('student-care.refunds.index', compact(
            'eligibleEnrollments', 'myRequests', 'stats'
        ));
    }

    /*
    |------------------------------------------------------------------
    | CS — Submit refund request
    |------------------------------------------------------------------
    */
    public function store(Request $request)
    {
        $request->validate([
            'enrollment_id' => 'required|exists:enrollment,enrollment_id',
            'reason'        => 'required|string|min:5',
        ]);

        $employeeId = Employee::where('user_id', auth()->id())->value('employee_id');
        $enrollment = Enrollment::with([
            'financialTransactions' => fn($q) => $q->where('transaction_type', 'Payment')
                                                    ->where('transaction_category', 'Course'),
        ])->findOrFail($request->enrollment_id);

        // Verify ownership
        if ($enrollment->created_by_cs_id !== $employeeId) {
            return back()->with('error', 'You can only request refunds for your own enrollments.');
        }

        // Check 3-day window
        $deposit = $enrollment->financialTransactions->first();
        if (!$deposit) {
            return back()->with('error', 'No payment found for this enrollment.');
        }
        if ($deposit->created_at->diffInDays(now()) > 3) {
            return back()->with('error', 'Refund window has expired (3 days from payment).');
        }

        // Check no existing pending request
        $existing = RefundRequest::where('enrollment_id', $enrollment->enrollment_id)
            ->whereIn('status', ['Pending', 'Approved'])
            ->first();
        if ($existing) {
            return back()->with('error', 'A refund request already exists for this enrollment.');
        }

        RefundRequest::create([
            'enrollment_id' => $enrollment->enrollment_id,
            'requested_by'  => $employeeId,
            'amount'        => $deposit->amount,
            'reason'        => $request->reason,
            'status'        => 'Pending',
        ]);

        return back()->with('success', 'Refund request submitted. Awaiting admin approval.');
    }
}