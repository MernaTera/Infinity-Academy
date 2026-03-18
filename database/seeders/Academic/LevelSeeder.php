<?php

namespace Database\Seeders\Academic;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Academic\Level;

class LevelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Level::insert([
            [
                'course_template_id' => 1,
                'name' => 'Level 1',
                'price' => 1500,
                'level_order' => 1,
                'total_hours' => 30,
                'default_session_duration' => 2,
                'max_capacity' => 15,
                'teacher_level' => 1,
                'created_by_admin_id' => 1
            ],
            [
                'course_template_id' => 1,
                'name' => 'Level 2',
                'price' => 2000,
                'level_order' => 2,
                'total_hours' => 30,
                'default_session_duration' => 2,
                'max_capacity' => 15,
                'teacher_level' => 2,
                'created_by_admin_id' => 1
            ],
            [
                'course_template_id' => 2,
                'name' => 'Level 1',
                'price' => 1200,
                'level_order' => 1,
                'total_hours' => 20,
                'default_session_duration' => 1.5,
                'max_capacity' => 10,
                'teacher_level' => 1,
                'created_by_admin_id' => 1
            ],
            [
                'course_template_id' => 2,
                'name' => 'Level 2',
                'price' => 1700,
                'level_order' => 2,
                'total_hours' => 20,
                'default_session_duration' => 1.5,
                'max_capacity' => 10,
                'teacher_level' => 2,
                'created_by_admin_id' => 1
            ]
        ]);
    }
}
