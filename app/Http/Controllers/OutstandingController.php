<?php

namespace App\Http\Controllers;

use App\Services\OutstandingService;
use Illuminate\Http\Request;
use App\Models\HR\Employee;
use App\Models\Enrollment\Enrollment;
use App\Models\Finance\FinancialTransaction;
use App\Models\Finance\RevenueSplit;
use App\Models\Enrollment\RestrictionLog;

class OutstandingController extends Controller
{
    protected OutstandingService $outstandingService;

    public function __construct(OutstandingService $outstandingService)
    {
        $this->outstandingService = $outstandingService;
    }

    public function index(Request $request)
    {
        $employee = Employee::where('user_id', auth()->id())->first();

        $data = $this->outstandingService->getOutstandingData($employee);

        return view('outstanding.index', array_merge($data, [
            'employee' => $employee,
        ]));
    }

    public function recordPayment(Request $request, $enrollmentId)
    {
        $request->validate([
            'amount'         => 'required|numeric|min:1',
            'payment_method' => 'required|in:Cash,Card,Transfer,Online',
            'notes'          => 'nullable|string|max:500',
        ]);

        $employee   = Employee::where('user_id', auth()->id())->first();
        $enrollment = Enrollment::with([
            'installmentSchedules' => fn($q) => $q->whereIn('status', ['Pending','Overdue'])->orderBy('due_date'),
            'financialTransactions',
            'paymentPlan',
        ])->findOrFail($enrollmentId);

        // حساب الـ remaining
        $paid      = $enrollment->financialTransactions
            ->whereIn('transaction_type', ['Payment','Installment'])->sum('amount')
            - $enrollment->financialTransactions
            ->where('transaction_type', 'Refund')->sum('amount');
        $remaining = max(0, $enrollment->final_price - $paid);

        if ($request->amount > $remaining) {
            return back()->with('error', 'Amount exceeds remaining balance.');
        }

        \Illuminate\Support\Facades\DB::transaction(function () use ($request, $enrollment, $employee) {

            
            $tx = FinancialTransaction::create([
                'enrollment_id'          => $enrollment->enrollment_id,
                'patch_id'               => $enrollment->patch_id,
                'branch_id'              => $employee->branch_id,
                'transaction_type'       => 'Installment',
                'transaction_category'   => 'Course',
                'amount'                 => $request->amount,
                'payment_method'         => $request->payment_method,
                'notes'                  => $request->notes,
                'created_by_employee_id' => $employee->employee_id,
            ]);

            RevenueSplit::create([
                'transaction_id'   => $tx->transaction_id,
                'employee_id'      => $enrollment->created_by_cs_id,
                'branch_id'        => $employee->branch_id,
                'patch_id'         => $enrollment->patch_id,
                'amount_allocated' => $request->amount,
                'allocation_type'  => 'Direct',
            ]);

    
            $amountLeft = (float) $request->amount;
            foreach ($enrollment->installmentSchedules as $inst) {
                if ($amountLeft <= 0) break;
                if ($amountLeft >= $inst->amount) {
                    $inst->update(['status' => 'Paid', 'paid_at' => now()]);
                    $amountLeft -= $inst->amount;
                }
            }


            $newPaid = FinancialTransaction::where('enrollment_id', $enrollment->enrollment_id)
                ->whereIn('transaction_type', ['Payment','Installment'])->sum('amount');
            $newRemaining = max(0, $enrollment->final_price - $newPaid);

            if ($newRemaining <= 0) {
                $enrollment->update(['restriction_flag' => false]);
                RestrictionLog::where('enrollment_id', $enrollment->enrollment_id)
                    ->whereNull('released_at')
                    ->update(['released_at' => now(), 'released_by' => $employee->employee_id]);
            }
        });

        return back()->with('success', 'Payment recorded successfully.');
    }
}