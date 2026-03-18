<?php

namespace Database\Seeders\Finance;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Finance\RefundRequest;
use App\Models\Enrollment\Enrollment;
use App\Models\HR\Employee;

class RefundRequestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $enrollment = Enrollment::first();
        $employee = Employee::first();

        RefundRequest::create([
            'enrollment_id' => $enrollment->enrollment_id,
            'requested_by' => $employee->employee_id,
            'amount' => 500,
            'status' => 'Pending',
        ]);
    }
}
