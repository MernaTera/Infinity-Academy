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
use App\Models\Leads\Lead;
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

    /*
    |------------------------------------------------------------------
    | Approve
    |------------------------------------------------------------------
    */
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

            // ── Activate enrollment ───────────────────────────────────
            $enrollment->update(['status' => 'Active']);

            // ── Calculate installment amount ──────────────────────────
            $remaining      = $enrollment->final_price * (1 - $plan->deposit_percentage / 100);
            $installmentAmt = $plan->installment_count > 0
                ? round($remaining / $plan->installment_count, 2)
                : 0;

            $currentPatch = $enrollment->patch;
            $branchId     = $adminEmployee?->branch_id ?? $currentPatch?->branch_id;
            $patchId      = $enrollment->patch_id ?? $currentPatch?->patch_id;

            // ── Cleanup any orphaned installments ─────────────────────
            $existingSchedules = InstallmentSchedule::where('enrollment_id', $enrollment->enrollment_id)->get();
            foreach ($existingSchedules as $sched) {
                FinancialTransaction::where('transaction_id', $sched->transaction_id)
                    ->where('transaction_type', 'Installment')
                    ->delete();
            }
            InstallmentSchedule::where('enrollment_id', $enrollment->enrollment_id)->delete();

            // ── Create installment schedule ───────────────────────────
            for ($i = 1; $i <= $plan->installment_count; $i++) {

                $tx = FinancialTransaction::create([
                    'enrollment_id'          => $enrollment->enrollment_id,
                    'patch_id'               => $patchId ?? null,
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
                    'due_date'           => null, // ✅ يتحدد لما SC تحط الطالب في course
                    'amount'             => $installmentAmt,
                    'status'             => 'Pending',
                ]);
            }

            // ── Update lead to Registered ─────────────────────────────
            $lead = Lead::where('student_id', $enrollment->student_id)->first();
            $lead?->update(['status' => 'Registered']);

            // ── Update log ────────────────────────────────────────────
            $log->update([
                'status'               => 'Approved',
                'approved_by_admin_id' => $adminEmployee?->employee_id,
                'approved_at'          => now(),
            ]);

            // ── Notify CS ─────────────────────────────────────────────
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

        return back()->with('success', 'Request approved and student registered successfully.');
    }

    /*
    |------------------------------------------------------------------
    | Reject
    |------------------------------------------------------------------
    */
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
            $enrollmentId  = $enrollment->enrollment_id;

            // ── 1. حذف الـ financial records ─────────────────────────
            $txIds = FinancialTransaction::where('enrollment_id', $enrollmentId)
                ->pluck('transaction_id');
            RevenueSplit::whereIn('transaction_id', $txIds)->delete();
            FinancialTransaction::where('enrollment_id', $enrollmentId)->delete();
            InstallmentSchedule::where('enrollment_id', $enrollmentId)->delete();
            DB::table('deposit_payment')->where('enrollment_id', $enrollmentId)->delete();

            // ── 2. حذف المواد ─────────────────────────────────────────
            \App\Models\Enrollment\EnrollmentMaterial::where('enrollment_id', $enrollmentId)->delete();

            // ── 3. حذف الـ placement test لو موجود ────────────────────
            if ($enrollment->placement_test_id) {
                \App\Models\Enrollment\PlacementTest::where('test_id', $enrollment->placement_test_id)->delete();
                $enrollment->update(['placement_test_id' => null]);
            }

            // ── 4. الـ enrollment يفضل موجود بـ Cancelled ─────────────
            $enrollment->update(['status' => 'Cancelled']);

            // ── 5. Lead يرجع Waiting ──────────────────────────────────
            $studentName = $enrollment->student?->full_name ?? 'student';
            $lead = Lead::where('student_id', $enrollment->student_id)->first();
            if ($lead) {
                $studentName = $lead->full_name;
                $lead->update([
                    'status'     => 'Waiting',
                    'student_id' => null,
                ]);
            }

            // ── 6. Student يبقى Inactive ──────────────────────────────
            $enrollment->student?->update([
                'is_active'     => false,
                'global_status' => 'Inactive',
            ]);

            // ── 7. Update log ─────────────────────────────────────────
            $log->update([
                'status'               => 'Rejected',
                'approved_by_admin_id' => $adminEmployee?->employee_id,
                'approved_at'          => now(),
                'rejection_note'       => $studentName . '||' . $request->reason,
            ]);

            // ── 8. Notify CS ──────────────────────────────────────────
            $csEmployeeId = $log->request_by_cs_id;
            if ($csEmployeeId) {
                DB::table('user_notification')->insert([
                    'employee_id'         => $csEmployeeId,
                    'title'               => 'Installment Request Declined',
                    'message'             => 'Your request for ' . $studentName .
                                            ' was declined. Reason: ' . $request->reason,
                    'related_entity_type' => 'installment_approval',
                    'related_entity_id'   => $enrollmentId,
                    'is_read'             => false,
                    'created_at'          => now(),
                    'updated_at'          => now(),
                ]);
            }
        });

        return back()->with('success', 'Request rejected.');
    }

    /*
    |------------------------------------------------------------------
    | AJAX — CS polls this to check approval status
    |------------------------------------------------------------------
    */
    public function checkStatus($enrollmentId)
    {
        $enrollment = Enrollment::find($enrollmentId);

        $log = InstallmentApprovalLog::where('enrollment_id', $enrollmentId)
            ->latest()
            ->first();

        $note = $log?->rejection_note;
        if ($note && str_contains($note, '||')) {
            $note = explode('||', $note)[1];
        }

        if (!$enrollment) {
            return response()->json([
                'status'          => 'Cancelled',
                'approval_status' => $log?->status ?? 'Rejected',
                'rejection_note'  => $note,
            ]);
        }

        return response()->json([
            'status'          => $enrollment->status,
            'approval_status' => $log?->status,
            'rejection_note'  => $note,
        ]);
    }
}