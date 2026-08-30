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


            $refundCategories = ['Course'];
            if ($refund->includes_material) {
                $refundCategories[] = 'Material';
            }


            $removableTx = $enrollment->financialTransactions->filter(function ($t) use ($refundCategories) {
                if ($t->transaction_type === 'Installment') return true;              
                if ($t->transaction_type === 'Payment') {
                    return in_array($t->transaction_category, $refundCategories);      
                }
                return false;
            });

            $txIds = $removableTx->pluck('transaction_id')->all();

            if (!empty($txIds)) {
                RevenueSplit::whereIn('transaction_id', $txIds)->delete();
            }

            InstallmentSchedule::where('enrollment_id', $enrollment->enrollment_id)->delete();

            if (!empty($txIds)) {
                FinancialTransaction::whereIn('transaction_id', $txIds)->delete();
            }

            $refund->update([
                'status'                   => 'Approved',
                'approved_by_admin_id'     => $adminEmployee->employee_id,
                'approved_at'              => now(),
                'processed_transaction_id' => null,
            ]);

            $enrollment->update(['status' => 'Cancelled']);

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