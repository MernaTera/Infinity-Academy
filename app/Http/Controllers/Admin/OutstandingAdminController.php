<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Enrollment\Enrollment;
use App\Models\Finance\InstallmentSchedule;
use App\Models\Enrollment\RestrictionLog;
use App\Models\HR\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Services\AuditService;

class OutstandingAdminController extends Controller
{
    public function index()
    {
        $allEnrollments = Enrollment::with([
            'student',
            'courseInstance.courseTemplate',
            'courseInstance.patch',
            'createdByCs',
            'paymentPlan',
            'installmentSchedules' => fn($q) => $q->orderBy('due_date'),
            'restrictionLogs'      => fn($q) => $q->whereNull('released_at'),
            'financialTransactions',
        ])
        ->whereIn('status', ['Active', 'Restricted'])
        ->get();

        $enrollments = $allEnrollments->filter(function ($e) {
            $totalFees = (float) $e->final_price
                + (float) $e->financialTransactions->where('transaction_category', 'Material')->sum('amount')
                + (float) $e->financialTransactions->where('transaction_category', 'Test')->sum('amount');

            $paidInstallmentIds = $e->installmentSchedules
                ->where('status', 'Paid')
                ->pluck('transaction_id')
                ->filter()
                ->toArray();

            $paid = (float) $e->financialTransactions
                    ->where('transaction_type', 'Payment')
                    ->sum('amount')
                + (float) $e->financialTransactions
                    ->where('transaction_type', 'Installment')
                    ->whereIn('transaction_id', $paidInstallmentIds)
                    ->sum('amount');

            $refunded = (float) $e->financialTransactions
                ->where('transaction_type', 'Refund')
                ->sum('amount');

            $balance              = $totalFees - ($paid - $refunded);
            $e->total_fees        = $totalFees;
            $e->remaining_balance = $balance;
            $e->total_paid        = $paid - $refunded;
            return $balance > 0;
        });

        $stats = [
            'total_outstanding' => $enrollments->sum('remaining_balance'),
            'count'             => $enrollments->count(),
            'restricted'        => $enrollments->where('status', 'Restricted')->count(),
            'overdue'           => $enrollments->filter(fn($e) =>
                $e->installmentSchedules->where('status', 'Overdue')->isNotEmpty()
            )->count(),
        ];

        return view('admin.outstanding.index', compact('enrollments', 'stats'));
    }

    public function override(Request $request, $id)
    {
        $enrollment = Enrollment::with('restrictionLogs')->findOrFail($id);
        $adminId    = Employee::where('user_id', auth()->id())->first()?->employee_id;

        $request->validate([
            'action' => 'required|in:lift,restrict,extend_due',
            'notes'  => 'nullable|string',
        ]);

        $oldStatus = $enrollment->status;
        DB::transaction(function () use ($request, $enrollment, $adminId) {

            if ($request->action === 'lift') {
                // Release restriction
                $enrollment->update([
                    'status'             => 'Active',
                    'restriction_flag'   => false,
                    'restriction_reason' => null,
                ]);

                RestrictionLog::where('enrollment_id', $enrollment->enrollment_id)
                    ->whereNull('released_at')
                    ->update([
                        'released_at'  => now(),
                        'released_by'  => $adminId,
                        'notes'        => $request->notes ?? 'Admin override — restriction lifted',
                    ]);

            } elseif ($request->action === 'restrict') {
                // Manual restriction
                $enrollment->update([
                    'status'             => 'Restricted',
                    'restriction_flag'   => true,
                    'restriction_reason' => 'ADMIN_MANUAL',
                ]);

                RestrictionLog::create([
                    'enrollment_id' => $enrollment->enrollment_id,
                    'triggered_by'  => 'Admin',
                    'reason'        => 'admin_manual',
                    'triggered_at'  => now(),
                    'notes'         => $request->notes,
                ]);

        } elseif ($request->action === 'extend_due') {
            $request->validate(['new_due_date' => 'required|date|after:today']);

            InstallmentSchedule::where('enrollment_id', $enrollment->enrollment_id)
                ->whereIn('status', ['Pending', 'Overdue'])
                ->orderBy('due_date')
                ->first()
                ?->update([
                    'due_date' => $request->new_due_date,
                    'status'   => 'Pending',
                ]);

            $enrollment->update([
                'status'             => 'Active',
                'restriction_flag'   => false,
                'restriction_reason' => null,
            ]);

            RestrictionLog::where('enrollment_id', $enrollment->enrollment_id)
                ->whereNull('released_at')
                ->update([
                    'released_at' => now(),
                    'released_by' => $adminId,
                    'notes'       => $request->notes ?? 'Admin override — due date extended',
                ]);
        }
        });

        AuditService::updated('enrollment', $id, 'status',
            $oldStatus,
            $request->action === 'lift' ? 'Active' : 'Restricted'
        );
        
        return back()->with('success', 'Action applied successfully.');
    }
}