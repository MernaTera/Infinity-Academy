<?php

namespace Database\Seeders\HR;

use Illuminate\Database\Seeder;
use App\Models\HR\ContractType;
use App\Models\HR\Teacher;
use App\Models\Academic\Patch;
use App\Models\HR\Employee;

class ContractTypeSeeder extends Seeder
{
    public function run(): void
    {
        $teachers = Teacher::all();
        $patches = Patch::all();
        $admin = Employee::first();

        $types = ['PT', 'FT', 'OT'];

        $data = [];

        foreach ($teachers as $teacher) {
            foreach ($patches as $patch) {

                $data[] = [
                    'teacher_id' => $teacher->teacher_id,
                    'patch_id' => $patch->patch_id,
                    'contract_type' => $types[array_rand($types)],
                    'max_sessions_allowed' => rand(10, 30),
                    'created_by_admin_id' => $admin->employee_id,
                ];
            }
        }

        ContractType::insert($data);
    }
}