<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Finance\InstallmentApprovalLog;
use App\Models\Finance\InstallmentSchedule;
use App\Models\Finance\FinancialTransaction;
use App\Models\Finance\RevenueSplit;
use App\Models\Enrollment\Enrollment;
use App\Models\HR\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InstallmentApprovalController extends Controller
{
    public function index()
    {
        $pending = InstallmentApprovalLog::with([
            'enrollment.student',
            'enrollment.courseTemplate',
            'enrollment.patch',
            'paymentPlan',
            'requestedBy',
        ])
        ->where('status', 'Pending')
        ->latest()
        ->get();

        $history = InstallmentApprovalLog::with([
            'enrollment.student',
            'approvedBy',
        ])
        ->whereIn('status', ['Approved', 'Rejected'])
        ->latest('approved_at')
        ->limit(20)
        ->get();

        $stats = [
            'pending'  => InstallmentApprovalLog::where('status', 'Pending')->count(),
            'approved' => InstallmentApprovalLog::where('status', 'Approved')->count(),
            'rejected' => InstallmentApprovalLog::where('status', 'Rejected')->count(),
        ];

        return view('admin.installments.index', compact('pending', 'history', 'stats'));
    }

    public function approve(Request $request, $id)
    {
        $log = InstallmentApprovalLog::findOrFail($id);

        if ($log->status !== 'Pending') {
            return back()->with('error', 'This request has already been processed.');
        }

        DB::transaction(function () use ($log) {
            $adminEmployee = Employee::where('user_id', auth()->id())->first();
            $enrollment    = $log->enrollment;
            $plan          = $log->paymentPlan;

            // 1. Activate enrollment
            $enrollment->update(['status' => 'Active']);

            // 2. Create installment schedule
            $remaining     = $enrollment->final_price * (1 - $plan->deposit_percentage / 100);
            $installmentAmt = $plan->installment_count > 0
                ? round($remaining / $plan->installment_count, 2)
                : 0;

            $currentPatch = $enrollment->patch;
            $branchId     = $adminEmployee?->branch_id ?? $currentPatch?->branch_id;

            for ($i = 1; $i <= $plan->installment_count; $i++) {
                $dueDate = now()->addDays($plan->grace_period_days * $i);

                // Create financial transaction for each installment
                $tx = FinancialTransaction::create([
                    'enrollment_id'          => $enrollment->enrollment_id,
                    'patch_id'               => $enrollment->patch_id,
                    'branch_id'              => $branchId,
                    'transaction_type'       => 'Installment',
                    'transaction_category'   => 'Course',
                    'amount'                 => $installmentAmt,
                    'payment_method'         => 'Cash',
                    'created_by_employee_id' => $adminEmployee?->employee_id,
                ]);

                InstallmentSchedule::create([
                    'enrollment_id'      => $enrollment->enrollment_id,
                    'transaction_id'     => $tx->transaction_id,
                    'installment_number' => $i,
                    'due_date'           => $dueDate,
                    'amount'             => $installmentAmt,
                    'status'             => 'Pending',
                ]);
            }

            // 3. Update approval log
            $log->update([
                'status'               => 'Approved',
                'approved_by_admin_id' => $adminEmployee?->employee_id,
                'approved_at'          => now(),
            ]);

            // 4. Notify the CS who made the request (user_notification table)
            $csEmployeeId = $log->request_by_cs_id;
            if ($csEmployeeId) {
                DB::table('user_notification')->insert([
                    'employee_id'         => $csEmployeeId,
                    'title'               => 'Installment Request Approved',
                    'message'             => 'Your installment plan request for ' .
                                            ($enrollment->student?->full_name ?? 'student') .
                                            ' has been approved by the admin.',
                    'related_entity_type' => 'installment_approval',
                    'related_entity_id'   => $enrollment->enrollment_id,
                    'is_read'             => false,
                    'created_at'          => now(),
                    'updated_at'          => now(),
                ]);
            }
        });

        return back()->with('success', 'Request approved and installment schedule created.');
    }

    public function reject(Request $request, $id)
    {
        $request->validate(['reason' => 'required|string|min:5']);

        $log = InstallmentApprovalLog::findOrFail($id);

        if ($log->status !== 'Pending') {
            return back()->with('error', 'This request has already been processed.');
        }

        DB::transaction(function () use ($log, $request) {
            $adminEmployee = Employee::where('user_id', auth()->id())->first();
            $enrollment    = $log->enrollment;

            // 1. Cancel enrollment
            $enrollment->update(['status' => 'Cancelled']);

            // 2. Update log
            $log->update([
                'status'               => 'Rejected',
                'approved_by_admin_id' => $adminEmployee?->employee_id,
                'approved_at'          => now(),
                'rejection_note'       => $request->reason,
            ]);

            // 3. Notify CS
            $csEmployeeId = $log->request_by_cs_id;
            if ($csEmployeeId) {
                DB::table('user_notification')->insert([
                    'employee_id'         => $csEmployeeId,
                    'title'               => 'Installment Request Declined',
                    'message'             => 'Your installment plan request for ' .
                                            ($enrollment->student?->full_name ?? 'student') .
                                            ' was declined. Reason: ' . $request->reason,
                    'related_entity_type' => 'installment_approval',
                    'related_entity_id'   => $enrollment->enrollment_id,
                    'is_read'             => false,
                    'created_at'          => now(),
                    'updated_at'          => now(),
                ]);
            }
        });

        return back()->with('success', 'Request rejected.');
    }

    // ── AJAX: CS polls this to check approval status ──
    public function checkStatus($enrollmentId)
    {
        $enrollment = Enrollment::find($enrollmentId);
        if (!$enrollment) return response()->json(['status' => 'not_found'], 404);

        $log = InstallmentApprovalLog::where('enrollment_id', $enrollmentId)
            ->latest()
            ->first();

        return response()->json([
            'status'         => $enrollment->status,
            'approval_status'=> $log?->status,
            'rejection_note' => $log?->rejection_note,
        ]);
    }
}