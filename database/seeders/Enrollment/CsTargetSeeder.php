<?php

namespace Database\Seeders\Enrollment;

use Illuminate\Database\Seeder;
use App\Models\Enrollment\CsTarget;
use App\Models\HR\Employee;
use App\Models\Academic\Patch;

class CsTargetSeeder extends Seeder
{
    public function run(): void
    {
        $employees = Employee::all();
        $patches = Patch::all();
        $admin = Employee::first();

        $data = [];

        foreach ($employees as $employee) {
            foreach ($patches as $patch) {
                $data[] = [
                    'employee_id' => $employee->employee_id,
                    'patch_id' => $patch->patch_id,
                    'target_amount' => rand(5000, 20000),
                    'target_registrations' => rand(10, 50),
                    'created_by_admin_id' => $admin->employee_id,
                ];
            }
        }

        CsTarget::insert($data);
    }
}