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
                    'due_date'           => null,
                    'amount'             => $installmentAmt,
                    'status'             => 'Pending',
                ]);
            }

            $lead = Lead::where('student_id', $enrollment->student_id)->first();
            $oldLeadStatus = $lead?->status;

            $lead?->update(['status' => 'Registered']);

            $log->update([
                'status'               => 'Approved',
                'approved_by_admin_id' => $adminEmployee?->employee_id,
                'approved_at'          => now(),
            ]);

            $waitingMeta = $log->waiting_list_meta ? json_decode($log->waiting_list_meta, true) : null;
            if ($waitingMeta) {
                $waiting = \App\Models\Enrollment\WaitingList::create([
                    'enrollment_id'           => $enrollment->enrollment_id,
                    'requested_patch_id'      => $waitingMeta['requested_patch_id']      ?? null,
                    'preferred_type'          => $waitingMeta['preferred_type']          ?? null,
                    'preferred_delivery_type' => $waitingMeta['preferred_delivery_type'] ?? null,
                    'preferred_delivery_mood' => $waitingMeta['preferred_delivery_mood'] ?? null,
                    'preferred_start_date'    => $waitingMeta['preferred_start_date']    ?? null,
                    'status'                  => 'Active',
                    'notes'                   => $waitingMeta['notes']                   ?? null,
                    'created_by_cs_id'        => $log->request_by_cs_id,
                ]);

                \DB::afterCommit(function () use ($waiting) {
                    event(new \App\Events\WaitingListUpdated($waiting));
                });
            }

            if ($lead) {
                $courseName = $enrollment->courseTemplate?->name 
                    ?? \App\Models\Academic\CourseTemplate::find($enrollment->course_template_id)?->name 
                    ?? 'course';

                \App\Services\LeadActivityLogger::for($lead)
                    ->action('Registered')
                    ->status($oldLeadStatus, 'Registered')
                    ->reason("Admin approved installment plan for \"{$courseName}\" (Enrollment #{$enrollment->enrollment_id})")
                    ->record();
            }
            $csEmployeeId = $log->request_by_cs_id;
            if ($csEmployeeId) {
                $studentName = $enrollment->student?->full_name ?? 'student';
                \App\Services\NotificationService::send(
                    (int) $csEmployeeId,
                    '✅ Installment Request Approved',
                    "Your installment plan request for {$studentName} has been approved.",
                    'installment_approved',
                    $enrollment->enrollment_id
                );
            }
            $leadEnrollment = \App\Models\Enrollment\Enrollment::with('student')->find($enrollment->enrollment_id);
            $lead = $leadEnrollment?->lead_id 
                ? \App\Models\Leads\Lead::find($leadEnrollment->lead_id)
                : \App\Models\Leads\Lead::where('student_id', $leadEnrollment?->student_id)->first();

            if ($lead) {
                $courseName = $enrollment->courseTemplate?->name 
                    ?? \App\Models\Academic\CourseTemplate::find($enrollment->course_template_id)?->name 
                    ?? 'course';

                \App\Services\LeadActivityLogger::for($lead)
                    ->action('Registered')
                    ->status($lead->status, 'Registered')
                    ->reason("Admin approved installment plan for \"{$courseName}\" (Enrollment #{$enrollment->enrollment_id})")
                    ->record();

                // Also update the lead status if it wasn't already Registered
                if ($lead->status !== 'Registered') {
                    $lead->update(['status' => 'Registered']);
                }
            }
        });

        return redirect()->route('admin.installments.index')->with('success', 'Request approved.');
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

            \App\Models\Enrollment\EnrollmentMaterial::where('enrollment_id', $enrollmentId)->delete();

            if ($enrollment->placement_test_id) {
                \App\Models\Enrollment\PlacementTest::where('test_id', $enrollment->placement_test_id)->delete();
                $enrollment->update(['placement_test_id' => null]);
            }

            $enrollment->update(['status' => 'Cancelled']);

            $studentName = $enrollment->student?->full_name ?? 'student';
            $lead = Lead::where('student_id', $enrollment->student_id)->first();
            if ($lead) {
                $studentName = $lead->full_name;
                $lead->update([
                    'student_id' => null,
                ]);
            }
            $enrollment->student?->update([
                'is_active'     => false,
                'global_status' => 'Inactive',
            ]);

            $log->update([
                'status'               => 'Rejected',
                'approved_by_admin_id' => $adminEmployee?->employee_id,
                'approved_at'          => now(),
                'rejection_note'       => $studentName . '||' . $request->reason,
            ]);

            if ($lead) {
                $courseName = $enrollment->courseTemplate?->name 
                    ?? \App\Models\Academic\CourseTemplate::find($enrollment->course_template_id)?->name 
                    ?? 'course';

                \App\Services\LeadActivityLogger::for($lead)
                    ->action('Note_Added')
                    ->reason("Admin declined installment plan for \"{$courseName}\" (Enrollment #{$enrollment->enrollment_id})")
                    ->notes("Rejection reason: {$request->reason}")
                    ->record();
            }

            // ── 8. Notify CS via real-time ─────────────────────────
            $csEmployeeId = $log->request_by_cs_id;
            if ($csEmployeeId) {
                \App\Services\NotificationService::send(
                    (int) $csEmployeeId,
                    '❌ Installment Request Declined',
                    "Your request for {$studentName} was declined. Reason: {$request->reason}",
                    'installment_rejected',
                    $enrollmentId
                );
            }
            $leadEnrollment = \App\Models\Enrollment\Enrollment::with('student')->find($enrollmentId);
            $lead = $leadEnrollment?->lead_id 
                ? \App\Models\Leads\Lead::find($leadEnrollment->lead_id)
                : \App\Models\Leads\Lead::where('student_id', $leadEnrollment?->student_id)->first();

            if ($lead) {
                $courseName = $leadEnrollment?->courseTemplate?->name 
                    ?? \App\Models\Academic\CourseTemplate::find($leadEnrollment?->course_template_id)?->name 
                    ?? 'course';

                \App\Services\LeadActivityLogger::for($lead)
                    ->action('Note_Added')
                    ->reason("Admin declined installment plan for \"{$courseName}\" (Enrollment #{$enrollmentId})")
                    ->notes("Rejection reason: {$request->reason}")
                    ->record();
            }
        });

        return redirect()->route('admin.installments.index')->with('success', 'Request rejected.');
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