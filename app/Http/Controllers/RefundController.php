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
            // ALL Course + Material deposit payments (Test is never refundable,
            // Material is refunded only if the CS opts in). Course may be split
            // across several payment methods.
            'financialTransactions' => fn($q) => $q->where('transaction_type', 'Payment')
                                                    ->whereIn('transaction_category', ['Course', 'Material'])
                                                    ->orderBy('created_at'),
            'installmentSchedules',
            'refundRequests',
        ])
        ->where('created_by_cs_id', $employeeId)
        ->whereIn('status', ['Active', 'Pending_Approval', 'Waiting'])
        ->get()
        ->filter(function ($enrollment) {
            $deposits       = $enrollment->financialTransactions;
            $courseDeposits = $deposits->where('transaction_category', 'Course');
            if ($courseDeposits->isEmpty()) return false;

            // Within 3 days of the FIRST course deposit payment
            $firstPaid = $courseDeposits->sortBy('created_at')->first();
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
            'enrollment_id'     => 'required|exists:enrollment,enrollment_id',
            'reason'            => 'required|string|min:5',
            'include_material'  => 'nullable|boolean',
        ]);

        $employeeId = Employee::where('user_id', auth()->id())->value('employee_id');
        $enrollment = Enrollment::with([
            // Course + Material deposits (Test is never refundable)
            'financialTransactions' => fn($q) => $q->where('transaction_type', 'Payment')
                                                    ->whereIn('transaction_category', ['Course', 'Material']),
            'installmentSchedules',
        ])->findOrFail($request->enrollment_id);

        // Verify ownership
        if ($enrollment->created_by_cs_id !== $employeeId) {
            return back()->with('error', 'You can only request refunds for your own enrollments.');
        }

        $allDeposits = $enrollment->financialTransactions;
        $courseDeposits   = $allDeposits->where('transaction_category', 'Course');
        $materialDeposits = $allDeposits->where('transaction_category', 'Material');

        if ($courseDeposits->isEmpty()) {
            return back()->with('error', 'No course deposit found for this enrollment.');
        }

        // Course deposit is always refunded
        $courseTotal = (float) $courseDeposits->sum('amount');

        // Material only if the CS opted in AND material was actually paid
        $includeMaterial = $request->boolean('include_material');
        $materialTotal   = (float) $materialDeposits->sum('amount');
        $refundMaterial  = $includeMaterial && $materialTotal > 0;

        $refundTotal = $courseTotal + ($refundMaterial ? $materialTotal : 0);

        // 3-day window (from first course deposit payment)
        $firstPaid = $courseDeposits->sortBy('created_at')->first();
        if ($firstPaid->created_at->diffInDays(now()) > 3) {
            return back()->with('error', 'Refund window has expired (3 days from payment).');
        }

        // Block refund if any installment has already been paid
        $hasPaidInstallment = $enrollment->installmentSchedules
            ->firstWhere('status', 'Paid') !== null;
        if ($hasPaidInstallment) {
            return back()->with('error', 'This student has already paid an installment — deposit refund is no longer available.');
        }

        // No existing pending/approved request
        $existing = RefundRequest::where('enrollment_id', $enrollment->enrollment_id)
            ->whereIn('status', ['Pending', 'Approved'])
            ->first();
        if ($existing) {
            return back()->with('error', 'A refund request already exists for this enrollment.');
        }

        $refundRequest = RefundRequest::create([
            'enrollment_id'     => $enrollment->enrollment_id,
            'requested_by'      => $employeeId,
            'amount'            => $refundTotal,
            'includes_material' => $refundMaterial,
            'reason'            => $request->reason,
            'status'            => 'Pending',
        ]);

        // Notify only admins in this enrollment's branch (branches are isolated).
        $adminIds = \App\Support\BranchContext::adminEmployeeIdsForBranch($enrollment->branch_id);

        $studentName = $enrollment->student?->full_name ?? "Enrollment #{$enrollment->enrollment_id}";
        $matNote = $refundMaterial ? ' (incl. material)' : '';

        foreach ($adminIds as $adminId) {
            \App\Services\NotificationService::send(
                $adminId,
                'New Refund Request',
                "Refund requested for {$studentName} — " . number_format($refundTotal) . ' LE' . $matNote,
                'refund_request',
                $refundRequest->request_id
            );
        }

        return redirect()->route('refunds.index')->with('success', 'Refund request submitted. Awaiting admin approval.');
    }
}