<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Student\Student;
use App\Models\HR\Employee;
use Illuminate\Http\Request;

class AdminStudentController extends Controller
{
    public function index(Request $request)
    {
        $search   = $request->query('search');
        $csFilter = $request->query('cs_id');
        $status   = $request->query('status');

        $students = Student::with([
            'phones',
            'lead.owner',
            'enrollments' => fn($q) => $q->with([
                'courseTemplate',
                'level',
                'sublevel',
                'teacher',
                'paymentPlan',
                'financialTransactions',
                'installmentSchedules',
                'createdByCs',
            ])->latest(),
        ])
        ->when($search, function ($q) use ($search) {
            $invoiceId = null;
            if (preg_match('/^INV-?(\d+)$/i', trim($search), $m)) {
                $invoiceId = (int) $m[1];
            } elseif (ctype_digit(trim($search))) {
                $invoiceId = (int) trim($search);
            }

            $q->where(function ($sub) use ($search, $invoiceId) {
                $sub->where('full_name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhereHas('phones', fn($q2) =>
                        $q2->where('phone_number', 'like', "%{$search}%"));

                if ($invoiceId !== null) {
                    $sub->orWhereHas('enrollments', fn($q3) =>
                        $q3->where('enrollment_id', $invoiceId));
                }
            });
        })
        ->when($status, fn($q) => $q->where('status', $status))
        ->when($csFilter, fn($q) =>
            $q->whereHas('enrollments', fn($q2) =>
                $q2->where('created_by_cs_id', $csFilter)))
        ->latest()
        ->paginate(20)
        ->withQueryString();

        $students->getCollection()->transform(function ($s) {
        $s->total_paid = $s->enrollments->sum(function ($e) {
            $payments = $e->financialTransactions
                ->where('transaction_type', 'Payment')->sum('amount');
            $paidInstallmentTxIds = $e->installmentSchedules
                ->where('status', 'Paid')->pluck('transaction_id')->filter()->all();
            $installments = $e->financialTransactions
                ->where('transaction_type', 'Installment')
                ->whereIn('transaction_id', $paidInstallmentTxIds)
                ->sum('amount');
            return $payments + $installments;
        });
            $s->total_fees = $s->enrollments->sum(function ($e) {
                return $e->final_price
                    + $e->financialTransactions->where('transaction_category', 'Material')->sum('amount')
                    + $e->financialTransactions->where('transaction_category', 'Test')->sum('amount');
            });
            $s->remaining       = max(0, $s->total_fees - $s->total_paid);
            $s->active_enrollment = $s->enrollments->firstWhere('status', 'Active');
            $s->deposit_methods = \DB::table('deposit_payment')
                ->whereIn('enrollment_id', $s->enrollments->pluck('enrollment_id'))
                ->get()
                ->groupBy('method');
            return $s;
        });

        $csUsers = Employee::whereHas('user.role', fn($q) =>
            $q->where('role_name', 'Customer Service')
        )->get();

        $stats = [
            'total'      => Student::count(),
            'active'     => Student::where('status', 'Active')->count(),
            'archived'   => Student::where('status', 'Archived')->count(),
            'dropped'    => Student::where('status', 'Dropped')->count(),
        ];

        return view('admin.students.index', compact(
            'students', 'csUsers', 'stats', 'search', 'csFilter', 'status'
        ));
    }

    public function show($id)
    {
        $student = Student::with([
            'phones',
            'lead.owner',
            'lead.leadHistories.changedBy',
            'lead.courseTemplate',
            'lead.level',
            'enrollments' => fn($q) => $q->with([
                'courseTemplate',
                'level',
                'sublevel',
                'teacher',
                'paymentPlan',
                'financialTransactions',
                'installmentSchedules',
                'createdByCs',
                'placementTest',
                'courseInstance.sessions',
                'postponements',
            ])->latest(),
        ])->findOrFail($id);

        return view('admin.students.show', compact('student'));
    }
}