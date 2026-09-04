<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Finance\FinancialTransaction;
use App\Models\Enrollment\Enrollment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ManualTransactionController extends Controller
{
    public function index()
    {
        $enrollments = Enrollment::with(['student', 'courseTemplate'])
            ->orderByDesc('enrollment_id')
            ->get()
            ->map(function ($e) {
                return [
                    'id'     => $e->enrollment_id,
                    'label'  => ($e->student?->full_name ?? 'Student #' . $e->student_id)
                                . ' · ' . ($e->courseTemplate?->name ?? 'Course')
                                . ' · #' . $e->enrollment_id,
                ];
            });

        $unpaidInstallmentTxIds = \App\Models\Finance\InstallmentSchedule::whereIn('status', ['Pending', 'Overdue'])
            ->whereNotNull('transaction_id')
            ->pluck('transaction_id')
            ->all();

        $transactions = FinancialTransaction::with([
                'enrollment.student',
                'branch',
                'createdBy',
            ])
            ->when(!empty($unpaidInstallmentTxIds), fn($q) =>
                $q->whereNotIn('transaction_id', $unpaidInstallmentTxIds))
            ->orderByDesc('transaction_id')
            ->paginate(25);

        return view('admin.manual-transactions.index', compact('enrollments', 'transactions'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'enrollment_id'        => 'required|exists:enrollment,enrollment_id',
            'amount'               => 'required|numeric|min:0.01',
            'payment_method'       => 'required|in:Cash,Instapay,Vodafone_Cash',
            'transaction_category' => 'required|in:Course,Material,Test,Other',
            'reference_number'     => 'nullable|string|max:255',
            'notes'                => 'nullable|string',
        ]);

        $methodMap = [
            'Cash'          => 'Cash',
            'Instapay'      => 'Transfer',
            'Vodafone_Cash' => 'Online',
        ];

        $enrollment = Enrollment::findOrFail($data['enrollment_id']);

        FinancialTransaction::create([
            'enrollment_id'          => $enrollment->enrollment_id,
            'patch_id'               => $enrollment->patch_id,
            'branch_id'              => $enrollment->branch_id,
            'transaction_type'       => 'Payment',
            'transaction_category'   => $data['transaction_category'],
            'amount'                 => (float) $data['amount'],
            'payment_method'         => $methodMap[$data['payment_method']] ?? 'Cash',
            'reference_number'       => $data['reference_number'] ?? null,
            'notes'                  => $data['notes'] ?? null,
            'created_by_employee_id' => auth()->user()->employee?->employee_id,
        ]);

        return back()->with('success',
            'Transaction recorded — ' . number_format((float) $data['amount'], 2) . ' LE added successfully.');
    }
}