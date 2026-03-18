<?php

namespace Database\Seeders\Finance;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Finance\FinancialTransaction;
use App\Models\Enrollment\Enrollment;
use App\Models\HR\Employee;

class FinancialTransactionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $enrollments = Enrollment::all();
        $employee = Employee::first();

        $data = [];

        foreach ($enrollments as $enrollment) {
            $data[] = [
                'enrollment_id' => $enrollment->enrollment_id,
                'patch_id' => $enrollment->patch_id,
                'branch_id' => $enrollment->branch_id ?? 1,
                'created_by_employee_id' => $employee->employee_id,
                'transaction_type' => 'Payment',
                'transaction_category' => 'Course',
                'amount' => rand(1000, 5000),
                'payment_method' => 'Cash',
            ];
        }

        FinancialTransaction::insert($data);
    }
}
