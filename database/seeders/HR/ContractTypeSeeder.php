<?php

namespace Database\Seeders\HR;

use Illuminate\Database\Seeder;
use App\Models\HR\ContractType;
use App\Models\HR\Teacher;
use App\Models\Academic\Patch;
use App\Models\HR\Employee;
use App\Models\HR\TeacherContract;

class ContractTypeSeeder extends Seeder
{
    public function run(): void
    {
        $admin = Employee::first();

        $types = [
            ['name' => 'Part Time',  'max_sessions_allowed' => 8],
            ['name' => 'Full Time',  'max_sessions_allowed' => 9],
            ['name' => 'Overtime',   'max_sessions_allowed' => 15],
        ];

        foreach ($types as $type) {
            ContractType::create([
                'name'                 => $type['name'],
                'max_sessions_allowed' => $type['max_sessions_allowed'],
                'is_active'            => true,
                'created_by_admin_id'  => $admin->employee_id,
            ]);
        }


    }
}