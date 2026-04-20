<?php

namespace Database\Seeders\Finance;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Student\Student;
use App\Models\Student\StudentPhone;
use App\Models\Enrollment\Enrollment;
use App\Models\Finance\FinancialTransaction;
use App\Models\Finance\InstallmentSchedule;
use App\Models\Enrollment\RestrictionLog;
use App\Models\Academic\Patch;
use App\Models\Academic\CourseTemplate;
use App\Models\Finance\PaymentPlan;
use App\Models\HR\Employee;
use App\Models\Core\Branch;

class OutstandingBalanceSeeder extends Seeder
{
    public function run(): void
    {
        $csEmployee  = Employee::where('user_id', 2)->first(); 
        $patch       = Patch::where('status', 'Active')->first();
        $branch      = Branch::first();
        $paymentPlan = PaymentPlan::first();
        $courses     = CourseTemplate::take(5)->get();

        if (!$csEmployee || !$patch || !$branch || !$paymentPlan || $courses->isEmpty()) {
            $this->command->warn('Missing required data. Run base seeders first.');
            return;
        }

        $cases = [
            [
                'name'        => 'Ahmed Hassan',
                'phone'       => '01012345681',
                'total'       => 4800.00,
                'deposit_pct' => 50,
                'status'      => 'Active',        // دفع الـ deposit بس
                'restricted'  => false,
                'due_days'    => 7,               // due بعد 7 أيام — On Track
            ],
            [
                'name'        => 'Sara Mohamed',
                'phone'       => '01012345682',
                'total'       => 3600.00,
                'deposit_pct' => 50,
                'status'      => 'Active',
                'restricted'  => false,
                'due_days'    => -5,              // فات الـ due date — Overdue
            ],
            [
                'name'        => 'Khaled Ibrahim',
                'phone'       => '01012345683',
                'total'       => 6000.00,
                'deposit_pct' => 50,
                'status'      => 'Restricted',    // Restricted بسبب payment
                'restricted'  => true,
                'due_days'    => -14,             // overdue من 14 يوم
            ],
            [
                'name'        => 'Nour Ali',
                'phone'       => '01012345684',
                'total'       => 2400.00,
                'deposit_pct' => 50,
                'status'      => 'Active',
                'restricted'  => false,
                'due_days'    => -2,              // Overdue بيومين
            ],
            [
                'name'        => 'Omar Youssef',
                'phone'       => '01012345685',
                'total'       => 5200.00,
                'deposit_pct' => 25,              // دفع 25% بس
                'status'      => 'Restricted',
                'restricted'  => true,
                'due_days'    => -20,
            ],
        ];

        foreach ($cases as $i => $case) {

            $student = Student::create([
                'full_name'     => $case['name'],
                'degree'        => 'Graduate',
                'status' => 'Active',
                'is_active'     => true,
            ]);

            StudentPhone::create([
                'student_id'   => $student->student_id,
                'phone_number' => $case['phone'],
                'is_primary'   => true,
            ]);

            $course     = $courses[$i % $courses->count()];
            $finalPrice = $case['total'];

            $enrollment = Enrollment::create([
                'student_id'        => $student->student_id,
                'course_template_id'=> $course->course_template_id,
                'patch_id'          => $patch->patch_id,
                'enrollment_type'   => 'Group',
                'delivery_mood'     => 'Offline',
                'final_price'       => $finalPrice,
                'payment_plan_id'   => $paymentPlan->payment_plan_id,
                'status'            => $case['status'],
                'restriction_flag'  => $case['restricted'],
                'created_by_cs_id'  => $csEmployee->employee_id,
            ]);

            $depositAmount = ($finalPrice * $case['deposit_pct']) / 100;

            $depositTx = FinancialTransaction::create([
                'enrollment_id'          => $enrollment->enrollment_id,
                'patch_id'               => $patch->patch_id,
                'branch_id'              => $branch->branch_id,
                'transaction_type'       => 'Payment',
                'transaction_category'   => 'Course',
                'amount'                 => $depositAmount,
                'payment_method'         => 'Cash',
                'created_by_employee_id' => $csEmployee->employee_id,
            ]);

            $remainingAmount = $finalPrice - $depositAmount;
            $dueDate         = now()->addDays($case['due_days'])->toDateString();

            InstallmentSchedule::create([
                'enrollment_id'      => $enrollment->enrollment_id,
                'transaction_id'     => $depositTx->transaction_id,
                'installment_number' => 1,
                'due_date'           => $dueDate,
                'amount'             => $remainingAmount,
                'status'             => $case['due_days'] < 0 ? 'Overdue' : 'Pending',
            ]);

            if ($case['restricted']) {
                RestrictionLog::create([
                    'enrollment_id' => $enrollment->enrollment_id,
                    'triggered_by'  => 'System',
                    'reason'        => 'installment_violation',
                    'triggered_at'  => now(),  
                    'released_at'   => null,
                ]);
            }
        }

        $this->command->info('✅ Outstanding balance test data seeded successfully — 5 cases added.');
    }
}