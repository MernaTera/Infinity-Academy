<?php

namespace Database\Seeders\Finance;

use Illuminate\Database\Seeder;
use App\Models\Finance\PaymentPlan;
use App\Models\HR\Employee;

class PaymentPlanSeeder extends Seeder
{
    public function run(): void
    {
        $admin = Employee::first();

        PaymentPlan::insert([
            [
                'name' => 'Full Cash',
                'deposit_percentage' => 100,
                'installment_count' => 0,
                'grace_period_days' => 0,
                'requires_admin_approval' => 0,
                'created_by_admin_id' => $admin->employee_id
            ],
            [
                'name' => 'Installments',
                'deposit_percentage' => 30,
                'installment_count' => 3,
                'grace_period_days' => 7,
                'requires_admin_approval' => 1,
                'created_by_admin_id' => $admin->employee_id
            ]
        ]);
    }
}