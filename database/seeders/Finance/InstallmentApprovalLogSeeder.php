<?php

namespace Database\Seeders\Finance;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Finance\InstallmentApprovalLog;
use App\Models\Enrollment\Enrollment;
use App\Models\Finance\PaymentPlan;
use App\Models\HR\Employee;

class InstallmentApprovalLogSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $enrollment = Enrollment::first();
        $plan = PaymentPlan::first();
        $employee = Employee::first();

        if (!$enrollment || !$plan || !$employee) {
            return; 
        }

        InstallmentApprovalLog::create([
            'enrollment_id' => $enrollment->enrollment_id,
            'payment_plan_id' => $plan->payment_plan_id,
            'request_by_cs_id' => $employee->employee_id,
            'status' => 'Approved',
            'approved_by_admin_id' => $employee->employee_id,
            'approved_at' => now(),
        ]);
    }
}
