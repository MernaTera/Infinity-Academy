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
            // ALL Course-category deposit payments (the deposit may be split
            // across several payment methods: Cash + Instapay + Vodafone ...)
            'financialTransactions' => fn($q) => $q->where('transaction_type', 'Payment')
                                                    ->where('transaction_category', 'Course')
                                                    ->orderBy('created_at'),
            'installmentSchedules',
            'refundRequests',
        ])
        ->where('created_by_cs_id', $employeeId)
        ->whereIn('status', ['Active', 'Pending_Approval', 'Waiting'])
        ->get()
        ->filter(function ($enrollment) {
            $deposits = $enrollment->financialTransactions;
            if ($deposits->isEmpty()) return false;

            // Within 3 days of the FIRST deposit payment
            $firstPaid = $deposits->sortBy('created_at')->first();
            if ($firstPaid->created_at->diffInDays(now()) > 3) return false;

            // NOT eligible if any installment has already been paid
            $hasPaidInstallment = $enrollment->installmentSchedules
                ->firstWhere('status', 'Paid') !== null;
            if ($hasPaidInstallment) return false;

            return true;
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

        return view('leads.partials.refunds.index', compact(
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
            'installmentSchedules',
        ])->findOrFail($request->enrollment_id);

        // Verify ownership
        if ($enrollment->created_by_cs_id !== $employeeId) {
            return back()->with('error', 'You can only request refunds for your own enrollments.');
        }

        // Total Course deposit = sum of ALL Course-category payments (split methods)
        $deposits = $enrollment->financialTransactions;
        if ($deposits->isEmpty()) {
            return back()->with('error', 'No deposit payment found for this enrollment.');
        }
        $depositTotal = (float) $deposits->sum('amount');
        $firstPaid    = $deposits->sortBy('created_at')->first();

        // Check 3-day window (from first deposit payment)
        if ($firstPaid->created_at->diffInDays(now()) > 3) {
            return back()->with('error', 'Refund window has expired (3 days from payment).');
        }

        // Block refund if any installment has already been paid
        $hasPaidInstallment = $enrollment->installmentSchedules
            ->firstWhere('status', 'Paid') !== null;
        if ($hasPaidInstallment) {
            return back()->with('error', 'This student has already paid an installment — deposit refund is no longer available.');
        }

        // Check no existing pending request
        $existing = RefundRequest::where('enrollment_id', $enrollment->enrollment_id)
            ->whereIn('status', ['Pending', 'Approved'])
            ->first();
        if ($existing) {
            return back()->with('error', 'A refund request already exists for this enrollment.');
        }

        $refundRequest = RefundRequest::create([
            'enrollment_id' => $enrollment->enrollment_id,
            'requested_by'  => $employeeId,
            'amount'        => $depositTotal,
            'reason'        => $request->reason,
            'status'        => 'Pending',
        ]);

        $adminIds = DB::table('employee')
            ->join('users', 'employee.user_id', '=', 'users.id')
            ->join('role', 'users.role_id', '=', 'role.role_id')
            ->where('role.role_name', 'Admin')
            ->pluck('employee.employee_id');

        $studentName = $enrollment->student?->full_name ?? "Enrollment #{$enrollment->enrollment_id}";

        foreach ($adminIds as $adminId) {
            \App\Services\NotificationService::send(
                $adminId,
                'New Refund Request',
                "Refund requested for {$studentName} — " . number_format($depositTotal) . ' LE',
                'refund_request',
                $refundRequest->request_id
            );
        }

        return redirect()->route('refunds.index')->with('success', 'Refund request submitted. Awaiting admin approval.');
    }
}