<?php

namespace App\Services;

use App\Models\Enrollment\Enrollment;
use App\Models\HR\Employee;

class OutstandingService
{
    protected BalanceCalculator $calculator;

    public function __construct(BalanceCalculator $calculator)
    {
        $this->calculator = $calculator;
    }

    public function getOutstandingData(Employee $employee): array
    {
        $enrollments = $this->getEnrollments($employee);

        return [
            'rows'    => $this->buildRows($enrollments),
            'summary' => $this->buildSummary($enrollments),
        ];
    }

    private function getEnrollments(Employee $employee)
    {
        return Enrollment::with([
                'student',
                'courseTemplate',
                'patch',                             
                'courseInstance.courseTemplate',
                'courseInstance.patch',              
                'paymentPlan',
                'installmentSchedules' => fn($q) => $q->orderBy('due_date'),
                'restrictionLogs'      => fn($q) => $q->whereNull('released_at')->orderByDesc('triggered_at'),
                'financialTransactions',
                'createdByCs',
                'waitingLists',                      
            ])
            ->whereIn('status', ['Active', 'Restricted', 'Waiting'])
            ->whereNotNull('final_price')
            ->get();
    }

    private function buildRows($enrollments): \Illuminate\Support\Collection
    {
        return $enrollments->map(function ($e) {

            $data = $this->calculator->calculate($e);
            $paid      = $data['net_paid'];
            $total     = $data['total_fees'];
            $remaining = $data['remaining_balance'];

            $nextInstallment = $e->installmentSchedules
                ->whereIn('status', ['Pending', 'Overdue'])
                ->sortBy('due_date')
                ->first();

            $scheduledPendingIds = $e->installmentSchedules
                ->whereIn('status', ['Pending', 'Overdue'])
                ->pluck('transaction_id')
                ->filter()
                ->toArray();

            $activeRestriction = $e->restrictionLogs->first();
            $isRestricted      = $e->restriction_flag || $activeRestriction;

            $daysOverdue = null;
            if ($nextInstallment && $nextInstallment->due_date) {
                // An installment isn't overdue the moment its due date passes —
                // the payment plan gives a grace period first. Count overdue
                // days from the END of that grace window, so the screen matches
                // the restriction job (which only marks/restricts after grace).
                $grace     = (int) ($e->paymentPlan?->grace_period_days ?? 0);
                $graceEnds = \Carbon\Carbon::parse($nextInstallment->due_date)->startOfDay()->addDays($grace);
                $today     = now()->startOfDay();
                if ($today->gt($graceEnds)) {
                    $daysOverdue = (int) abs($today->diffInDays($graceEnds));
                }
            }

            $patchName = $this->resolvePatchName($e);

            $groupedTransactions = $this->groupTransactions($e, $scheduledPendingIds);

            return [
                'enrollment_id'    => $e->enrollment_id,
                'student_name'     => $e->student?->full_name ?? '—',
                'course'           => $e->courseTemplate?->name ?? $e->courseInstance?->courseTemplate?->name ?? '—',
                'patch'            => $patchName,
                'payment_plan'     => $e->paymentPlan?->name ?? '—',
                'total'            => $total,
                'paid'             => $paid,
                'remaining'        => $remaining,
                'is_finished'      => $remaining == 0,
                'next_due_date'    => $nextInstallment?->due_date?->format('d M Y'),
                'next_due_amount'  => $nextInstallment?->amount,
                'has_pending_installment' => $e->installmentSchedules
                                            ->whereIn('status', ['Pending', 'Overdue'])
                                            ->isNotEmpty(),
                'is_restricted'    => $isRestricted,
                'restriction_reason' => $activeRestriction?->reason ?? null,
                'days_overdue'     => $daysOverdue,
                'cs_name'          => $e->createdByCs?->full_name ?? '—',
                'enrollment_type'  => $e->enrollment_type,
                'installments'     => $e->installmentSchedules->map(fn($i) => [
                    'number'   => $i->installment_number,
                    'amount'   => $i->amount,
                    'due_date' => $i->due_date?->format('d M Y'),
                    'status'   => $i->status,
                    'paid_at'  => $i->paid_at?->format('d M Y'),
                ])->toArray(),
                'transactions' => $groupedTransactions,
            ];
        })->values();
    }

    private function groupTransactions($enrollment, array $scheduledPendingIds): array
    {
        $relevantTxs = $enrollment->financialTransactions
            ->filter(fn($t) =>
                in_array($t->transaction_type, ['Payment', 'Refund']) ||
                ($t->transaction_type === 'Installment' && !in_array($t->transaction_id, $scheduledPendingIds))
            );

        $grouped = $relevantTxs->groupBy(function ($t) {
            $minute = $t->created_at?->format('Y-m-d H:i') ?? '—';
            return "{$t->transaction_type}::{$t->transaction_category}::{$minute}";
        });

        return $grouped->map(function ($group) {
            $first  = $group->first();
            $total  = $group->sum('amount');
            $count  = $group->count();

            $methodsBreakdown = null;
            if ($count > 1) {
                $methodsBreakdown = $group
                    ->groupBy('payment_method')
                    ->map(fn($mgroup, $method) => [
                        'method' => $method,
                        'amount' => $mgroup->sum('amount'),
                    ])
                    ->values()
                    ->toArray();
            }

            return [
                'type'              => $first->transaction_type,
                'category'          => $first->transaction_category,
                'amount'            => $total,
                'method'            => $count > 1 ? 'Multi' : $first->payment_method,
                'methods_breakdown' => $methodsBreakdown,
                'method_count'      => $count,
                'notes'             => $first->notes,
                'date'              => $first->created_at?->format('d M Y'),
            ];
        })->sortByDesc('date')->values()->toArray();
    }


    private function resolvePatchName($enrollment): string
    {
        if ($enrollment->patch?->name) {
            return $enrollment->patch->name;
        }
        if ($enrollment->courseInstance?->patch?->name) {
            return $enrollment->courseInstance->patch->name;
        }
        $wl = $enrollment->waitingLists?->first();
        if ($wl) {
            if ($wl->requested_patch_id) {
                $reqPatch = \App\Models\Academic\Patch::find($wl->requested_patch_id);
                if ($reqPatch) {
                    $suffix = $wl->preferred_type === 'Next_Patch' ? ' · Next Patch' : '';
                    return $reqPatch->name . $suffix;
                }
            }
            if ($wl->preferred_type === 'Next_Patch') {
                return 'Next Patch (TBD)';
            }
            if ($wl->preferred_type === 'Specific_Date' && $wl->preferred_start_date) {
                return 'Custom Start · ' . \Carbon\Carbon::parse($wl->preferred_start_date)->format('d M Y');
            }
        }
        return '—';
    }

    private function buildSummary($enrollments): array
    {
        $rows = $this->buildRows($enrollments);

        return [
            'total_outstanding' => $rows->where('is_finished', false)->sum('remaining'),
            'total_students'    => $rows->where('is_finished', false)->count(),
            'restricted_count'  => $rows->where('is_restricted', true)->where('is_finished', false)->count(),
            'overdue_count'     => $rows->whereNotNull('days_overdue')->where('is_finished', false)->count(),
            'finished_count'    => $rows->where('is_finished', true)->count(),
        ];
    }
}