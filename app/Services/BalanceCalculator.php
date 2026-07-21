<?php

namespace App\Services;

use App\Models\Enrollment\Enrollment;

class BalanceCalculator
{
    public function calculate(Enrollment $enrollment): array
    {
        $totalFees = (float) $enrollment->final_price;

        $depositPaid = (float) $enrollment->financialTransactions
            ->where('transaction_type', 'Payment')
            ->where('transaction_category', 'Course')
            ->sum('amount');

        $paidInstallmentIds = $enrollment->installmentSchedules
            ->where('status', 'Paid')
            ->pluck('transaction_id')
            ->filter()
            ->toArray();

        $installmentsPaid = (float) $enrollment->financialTransactions
            ->where('transaction_type', 'Installment')
            ->whereIn('transaction_id', $paidInstallmentIds)
            ->sum('amount');

        $grossPaid = $depositPaid + $installmentsPaid;

        $refunded = (float) $enrollment->financialTransactions
            ->where('transaction_type', 'Refund')
            ->sum('amount');

        $netPaid = $grossPaid - $refunded;
        $balance = $totalFees - $netPaid;
        $balance = $balance < 0.02 ? 0 : round($balance, 2);

        return [
            'total_fees'        => $totalFees,
            'gross_paid'        => $grossPaid,
            'refunded'          => $refunded,
            'net_paid'          => max(0, $netPaid),
            'remaining_balance' => max(0, $balance),
        ];
    }

    public function getRemaining(Enrollment $enrollment): float
    {
        return $this->calculate($enrollment)['remaining_balance'];
    }
}