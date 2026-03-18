<?php

namespace Database\Seeders\HR;

use Illuminate\Database\Seeder;
use App\Models\HR\Teacher;
use App\Models\HR\Employee;
use App\Models\Academic\EnglishLevel;

class TeacherSeeder extends Seeder
{
    public function run(): void
    {
        $employees = Employee::all();
        $levels = EnglishLevel::all();

        $data = [];

        foreach ($employees->take(5) as $employee) { // أول 5 بس teachers
            $data[] = [
                'employee_id' => $employee->employee_id,
                'english_level_id' => $levels->random()->english_level_id,
                'is_active' => 1,
            ];
        }

        Teacher::insert($data);
    }
}