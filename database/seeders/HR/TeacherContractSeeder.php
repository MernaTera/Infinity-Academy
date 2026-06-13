<?php

namespace Database\Seeders\HR;

use Illuminate\Database\Seeder;
use App\Models\HR\TeacherContract;
use App\Models\HR\ContractType;
use App\Models\HR\Teacher;
use App\Models\Academic\Patch;
use App\Models\HR\Employee;

class TeacherContractSeeder extends Seeder
{
    public function run(): void
    {
        $teachers      = Teacher::all();
        $patches       = Patch::all();
        $admin         = Employee::first();
        $contractTypes = ContractType::all();

        if ($contractTypes->isEmpty()) {
            $this->command->warn('No contract types found. Run ContractTypeSeeder first.');
            return;
        }

        foreach ($teachers as $teacher) {
            foreach ($patches as $patch) {
                TeacherContract::firstOrCreate(
                    [
                        'teacher_id' => $teacher->teacher_id,
                        'patch_id'   => $patch->patch_id,
                    ],
                    [
                        'contract_type_id'     => $contractTypes->random()->contract_type_id,
                        'is_active'            => true,
                        'created_by_admin_id'  => $admin->employee_id,
                    ]
                );
            }
        }
    }
}