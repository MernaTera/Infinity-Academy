<?php

namespace Database\Seeders\Academic;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Academic\CourseInstance;

class CourseInstanceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        CourseInstance::insert([
            [
                'course_template_id' => 1,
                'level_id' => 1,
                'sublevel_id' => 1,
                'patch_id' => 1,
                'teacher_id' => 1,
                'branch_id' => 1,
                'start_date' => '2026-01-05',
                'end_date' => '2026-02-05',
                'room_id' => 1,
                'delivery_mood' => 'Offline',
                'total_hours' => 30,
                'session_duration' => 2,
                'capacity' => 15,
                'type' => 'Group',
                'created_by_employee_id' => 1
            ],
            [
                'course_template_id' => 1,
                'level_id' => 2,
                'sublevel_id' => 3,
                'patch_id' => 1,
                'teacher_id' => 2,
                'branch_id' => 1,
                'start_date' => '2026-01-06',
                'end_date' => '2026-02-06',
                'room_id' => 2,
                'delivery_mood' => 'Offline',
                'total_hours' => 30,
                'session_duration' => 2,
                'capacity' => 15,
                'type' => 'Group',
                'created_by_employee_id' => 1
            ]
        ]);
    }
}
