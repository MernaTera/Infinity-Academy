<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Student\Student;
use App\Models\Student\StudentPhone;
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
                'teacher.employee' => fn($q2) => $q2->withoutGlobalScope('branch'),
                'courseInstance.teacher.employee' => fn($q2) => $q2->withoutGlobalScope('branch'),
                'paymentPlan',
                'financialTransactions',
                'installmentSchedules',
                'createdByCs',
            ])->latest(),
        ])
        ->whereHas('enrollments', fn($q) => $q->where('status', '!=', 'Cancelled'))
        ->when($status, fn($q) =>
            $q->whereHas('enrollments', fn($q2) => $q2->where('status', $status)))
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

        $visibleStudents = fn() => Student::whereHas('enrollments', fn($q) => $q->where('status', '!=', 'Cancelled'));

        $stats = [
            'total'     => $visibleStudents()->count(),
            'active'    => $visibleStudents()->whereHas('enrollments', fn($q) => $q->where('status', 'Active'))->count(),
            'waiting'   => $visibleStudents()->whereHas('enrollments', fn($q) => $q->where('status', 'Waiting'))->count(),
            'completed' => $visibleStudents()->whereHas('enrollments', fn($q) => $q->where('status', 'Completed'))->count(),
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
                'teacher.employee' => fn($q2) => $q2->withoutGlobalScope('branch'),
                'paymentPlan',
                'financialTransactions',
                'installmentSchedules.financialTransaction',
                'createdByCs',
                'placementTest',
                'courseInstance.sessions',
                'courseInstance.teacher.employee' => fn($q2) => $q2->withoutGlobalScope('branch'),
                'postponements',
            ])->latest(),
        ])->findOrFail($id);

        return view('admin.students.show', compact('student'));
    }

    public function update(Request $request, $id)
    {
        $student = Student::findOrFail($id);

        $data = $request->validate([
            'full_name' => [
                'required', 'string', 'min:3', 'max:255',
                'regex:/^\s*([\p{Arabic}A-Za-z]{2,})(\s+[\p{Arabic}A-Za-z]{2,}){3,}\s*$/u',
            ],
            'email'     => [
                'nullable', 'email', 'max:255',
                \Illuminate\Validation\Rule::unique('student', 'email')->ignore($student->student_id, 'student_id'),
            ],
            'degree'    => ['required', 'in:Student,Graduate'],
            'birthdate' => ['nullable', 'date'],
            'location'  => ['nullable', 'string', 'max:255'],
        ], [
            'full_name.regex' => 'Please enter the full 4-part name (first, father, grandfather, and family name).',
            'degree.required' => 'Please select a degree.',
            'degree.in'       => 'Degree must be either Student or Graduate.',
            'email.unique'    => 'This email is already used by another student.',
        ]);

        $student->update($data);
        \App\Models\Leads\Lead::where('student_id', $student->student_id)->update([
            'full_name' => $data['full_name'],
            'degree'    => $data['degree'],
            'birthdate' => $data['birthdate'] ?? null,
            'location'  => $data['location'] ?? null,
        ]);

        return back()->with('success', 'Student information updated.');
    }

    /*
    |--------------------------------------------------------------------------
    | Phone numbers (multiple per student)
    |--------------------------------------------------------------------------
    */

    /**
     * Add a phone number to a student.
     * The first number added becomes the primary automatically.
     */
    public function storePhone(Request $request, $id)
    {
        $student = Student::findOrFail($id);

        $data = $request->validate([
            'phone_number' => [
                'required', 'string', 'max:20',
                'regex:/^[0-9+\-\s()]{6,20}$/',
                'unique:student_phone,phone_number',
            ],
        ], [
            'phone_number.unique' => 'This phone number is already registered.',
            'phone_number.regex'  => 'Please enter a valid phone number.',
            'phone_number.max'    => 'Phone number is too long.',
        ]);

        $isFirst = $student->phones()->count() === 0;
        $makePrimary = $isFirst || $request->boolean('is_primary');

        if ($makePrimary) {
            $student->phones()->update(['is_primary' => false]);
        }

        StudentPhone::create([
            'student_id'   => $student->student_id,
            'phone_number' => trim($data['phone_number']),
            'is_primary'   => $makePrimary,
        ]);

        return back()->with('success', 'Phone number added.');
    }

    /**
     * Mark one of the student's numbers as the primary one.
     */
    public function setPrimaryPhone($id, $phoneId)
    {
        $student = Student::findOrFail($id);
        $phone   = $student->phones()->where('phone_id', $phoneId)->firstOrFail();

        $student->phones()->update(['is_primary' => false]);
        $phone->update(['is_primary' => true]);

        return back()->with('success', 'Primary number updated.');
    }

    public function deletePhone($id, $phoneId)
    {
        $student = Student::findOrFail($id);
        $phone   = $student->phones()->where('phone_id', $phoneId)->firstOrFail();

        $wasPrimary = (bool) $phone->is_primary;
        $phone->delete();

        if ($wasPrimary) {
            $next = $student->phones()->oldest('phone_id')->first();
            if ($next) {
                $next->update(['is_primary' => true]);
            }
        }

        return back()->with('success', 'Phone number removed.');
    }
}