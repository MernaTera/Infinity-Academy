<?php

namespace App\Services;

use App\Models\Enrollment\Enrollment;
use App\Models\HR\Employee;
use Illuminate\Support\Facades\DB;
use App\Models\Finance\FinancialTransaction;
use App\Models\Finance\RevenueSplit;

class OutstandingService
{
    public function getOutstandingData(Employee $employee): array
    {
        $enrollments = $this->getEnrollments($employee);

        return [
            'rows'        => $this->buildRows($enrollments),
            'summary'     => $this->buildSummary($enrollments),
        ];
    }


    private function getEnrollments(Employee $employee)
    {
        return Enrollment::with([
                'student',
                'courseTemplate',
                'paymentPlan',
                'installmentSchedules' => fn($q) => $q->orderBy('due_date'),
                'restrictionLogs' => fn($q) => $q->whereNull('released_at')->orderByDesc('triggered_at'),
                'financialTransactions',
                'createdByCs',
            ])
            ->where('created_by_cs_id', $employee->employee_id)
            ->whereIn('status', ['Active', 'Restricted'])
            ->whereNotNull('final_price')
            ->get()
            ->filter(fn($e) => $this->getRemaining($e) > 0); 
    }


    private function buildRows($enrollments): \Illuminate\Support\Collection
    {
        return $enrollments->map(function ($e) {

            $paid      = $this->getPaid($e);
            $total     = (float) $e->final_price;
            $remaining = max(0, $total - $paid);

            $nextInstallment = $e->installmentSchedules
                ->whereIn('status', ['Pending', 'Overdue'])
                ->sortBy('due_date')
                ->first();

            $activeRestriction = $e->restrictionLogs->first();
            $isRestricted      = $e->restriction_flag || $activeRestriction;

            $daysOverdue = null;
            if ($nextInstallment) {
                $dueDate = $nextInstallment->due_date;
                if ($dueDate && now()->startOfDay()->gt(\Carbon\Carbon::parse($dueDate)->startOfDay())) {
                    $daysOverdue = now()->startOfDay()->diffInDays(\Carbon\Carbon::parse($dueDate)->startOfDay());
                }
            }

            return [
                'enrollment_id'    => $e->enrollment_id,
                'student_name'     => $e->student?->full_name ?? '—',
                'course'           => $e->courseTemplate?->name ?? '—',
                'payment_plan'     => $e->paymentPlan?->name ?? '—',
                'total'            => $total,
                'paid'             => $paid,
                'remaining'        => $remaining,
                'next_due_date'    => $nextInstallment?->due_date?->format('d M Y'),
                'next_due_amount'  => $nextInstallment?->amount,
                'is_restricted'    => $isRestricted,
                'restriction_reason'=> $activeRestriction?->reason ?? null,
                'days_overdue'     => $daysOverdue,
                'cs_name'          => $e->createdByCs?->full_name ?? '—',
                'enrollment_type'  => $e->enrollment_type,
                'installments' => $e->installmentSchedules->map(fn($i) => [
                    'number'   => $i->installment_number,
                    'amount'   => $i->amount,
                    'due_date' => $i->due_date?->format('d M Y'),
                    'status'   => $i->status,
                    'paid_at'  => $i->paid_at?->format('d M Y'),
                ])->toArray(),
                'transactions' => $e->financialTransactions
                ->whereIn('transaction_type', ['Payment','Installment','Refund'])
                ->map(fn($t) => [
                    'type'   => $t->transaction_type,
                    'amount' => $t->amount,
                    'method' => $t->payment_method,
                    'date'   => $t->created_at?->format('d M Y'),
                ])->toArray(),
            ];
        })->values();
    }


    private function buildSummary($enrollments): array
    {
        $rows = $this->buildRows($enrollments);

        return [
            'total_outstanding' => $rows->sum('remaining'),
            'total_students'    => $rows->count(),
            'restricted_count'  => $rows->where('is_restricted', true)->count(),
            'overdue_count'     => $rows->whereNotNull('days_overdue')->count(),
        ];
    }

    private function getPaid(Enrollment $enrollment): float
    {
        return (float) $enrollment->financialTransactions
            ->whereIn('transaction_type', ['Payment', 'Installment'])
            ->sum('amount')
            - (float) $enrollment->financialTransactions
            ->where('transaction_type', 'Refund')
            ->sum('amount');
    }

    private function getRemaining(Enrollment $enrollment): float
    {
        return max(0, (float) $enrollment->final_price - $this->getPaid($enrollment));
    }
}