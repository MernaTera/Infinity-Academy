<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Finance\RefundRequest;
use App\Models\Finance\FinancialTransaction;
use App\Models\Finance\RevenueSplit;
use App\Models\Finance\InstallmentSchedule;
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
            'enrollment.installmentSchedules',
            'enrollment.student',
            'enrollment',
        ])->findOrFail($id);

        if ($refund->status !== 'Pending') {
            return back()->with('error', 'Request is not pending.');
        }

        $adminEmployee = Employee::where('user_id', auth()->id())->first();
        $enrollment    = $refund->enrollment;

        DB::transaction(function () use ($refund, $enrollment, $adminEmployee) {

            // Decide which transaction categories are being refunded:
            //   - Course   → always refunded
            //   - Material  → only if the CS opted in (refund->includes_material)
            //   - Test      → NEVER refunded (student already took the test)
            //   - Installments → removed (registration is being reversed)
            $refundCategories = ['Course'];
            if ($refund->includes_material) {
                $refundCategories[] = 'Material';
            }

            // Transactions being removed:
            //   * all Payment transactions in the refunded categories
            //   * all Installment transactions (course installments)
            $removableTx = $enrollment->financialTransactions->filter(function ($t) use ($refundCategories) {
                if ($t->transaction_type === 'Installment') return true;               // course installments
                if ($t->transaction_type === 'Payment') {
                    return in_array($t->transaction_category, $refundCategories);       // Course (+Material if chosen)
                }
                return false; // Test payments (and anything else) stay
            });

            $txIds = $removableTx->pluck('transaction_id')->all();

            // 1. Remove RevenueSplit rows tied to the removed transactions
            //    → reverses the CS revenue for the refunded portion
            if (!empty($txIds)) {
                RevenueSplit::whereIn('transaction_id', $txIds)->delete();
            }

            // 2. Delete installment schedules for this enrollment
            InstallmentSchedule::where('enrollment_id', $enrollment->enrollment_id)->delete();

            // 3. Delete the refunded financial transactions
            //    (Test payments are intentionally left untouched → stay as revenue)
            if (!empty($txIds)) {
                FinancialTransaction::whereIn('transaction_id', $txIds)->delete();
            }

            // 4. Record approval on the refund request
            $refund->update([
                'status'                   => 'Approved',
                'approved_by_admin_id'     => $adminEmployee->employee_id,
                'approved_at'              => now(),
                'processed_transaction_id' => null,
            ]);

            // 5. Cancel the enrollment (drops out of Outstanding since not Active)
            $enrollment->update(['status' => 'Cancelled']);

            // 6. Restore the lead to Waiting + unlink the student
            $lead = \App\Models\Leads\Lead::where('student_id', $enrollment->student_id)->first();
            if ($lead) {
                $oldStatus = $lead->status;
                $lead->update([
                    'status'     => 'Waiting',
                    'student_id' => null,
                ]);

                $matNote = $refund->includes_material ? ' (incl. material)' : '';
                \App\Services\LeadActivityLogger::for($lead)
                    ->action('Status_Changed')
                    ->status($oldStatus, 'Waiting')
                    ->reason('Deposit refunded — registration reversed (Enrollment #' . $enrollment->enrollment_id . ')')
                    ->notes('Refund approved by admin. Amount: ' . number_format($refund->amount) . ' LE' . $matNote . '. Test fee (if any) retained.')
                    ->record();
            }
        });

        $studentName = $refund->enrollment?->student?->full_name ?? "Enrollment #{$refund->enrollment_id}";
        \App\Services\NotificationService::send(
            $refund->requested_by,
            'Refund Approved',
            "Your refund request for {$studentName} was approved — " . number_format($refund->amount) . ' LE. The lead is back in your Waiting list.',
            'refund_approved',
            $refund->request_id
        );

        return redirect()->route('admin.refunds.index')->with('success', 'Refund approved. Registration reversed and lead restored to Waiting.');
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

        $studentName = $refund->enrollment?->student?->full_name ?? "Enrollment #{$refund->enrollment_id}";
        \App\Services\NotificationService::send(
            $refund->requested_by,
            'Refund Rejected',
            "Your refund request for {$studentName} was rejected. Reason: " . $request->reason,
            'refund_rejected',
            $refund->request_id
        );

        return redirect()->route('admin.refunds.index')->with('success', 'Refund request rejected.');
    }
}