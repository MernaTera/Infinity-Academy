<?php

namespace App\Services;

use App\Models\Finance\FinancialTransaction;
use App\Models\Finance\InstallmentSchedule;
use App\Models\Finance\PaymentPlan;

class PaymentService
{
    public function createPayment($enrollment)
    {
        $plan = PaymentPlan::findOrFail($enrollment->payment_plan_id);

        // 1. Deposit
        $deposit = ($plan->deposit_percentage / 100) * $enrollment->final_price;

        $this->createTransaction($enrollment, $deposit);

        // 2. Installments
        if ($plan->installment_count > 0) {
            $this->createInstallments($enrollment, $plan, $deposit);
        }
    }

    private function createTransaction($enrollment, $amount)
    {
        return FinancialTransaction::create([
            'enrollment_id' => $enrollment->enrollment_id,
            'patch_id' => $enrollment->patch_id,
            'transaction_type' => 'Payment',
            'transaction_category' => 'Course',
            'amount' => $amount,
            'payment_method' => 'Cash',
            'created_by_employee_id' => $this->employeeId()
        ]);
    }

    private function createInstallments($enrollment, $plan, $deposit)
    {
        $remaining = $enrollment->final_price - $deposit;
        $perInstallment = $remaining / $plan->installment_count;

        for ($i = 1; $i <= $plan->installment_count; $i++) {

            InstallmentSchedule::create([
                'enrollment_id' => $enrollment->enrollment_id,
                'installment_number' => $i,
                'due_date' => now()->addWeeks($i),
                'amount' => $perInstallment,
                'status' => 'Pending'
            ]);
        }
    }

    private function employeeId()
    {
        return auth()->user()->employees->first()->employee_id;
    }
}
