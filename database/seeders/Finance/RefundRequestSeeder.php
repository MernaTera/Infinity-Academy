<?php

namespace Database\Seeders\Finance;

use Illuminate\Database\Seeder;
use App\Models\Finance\RefundRequest;
use App\Models\Enrollment\Enrollment;
use App\Models\HR\Employee;

class RefundRequestSeeder extends Seeder
{
    public function run(): void
    {
        $enrollment = Enrollment::first();
        $employee = Employee::first();

        if (!$enrollment || !$employee) {
            return;
        }

        RefundRequest::create([
            'enrollment_id' => $enrollment->enrollment_id,
            'requested_by' => $employee->employee_id,
            'amount' => 500,
            'reason' => 'Student requested refund',
            'status' => 'Pending',
        ]);
    }
}
