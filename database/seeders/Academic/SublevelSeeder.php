<?php

namespace Database\Seeders\Academic;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Academic\Sublevel;

class SublevelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Sublevel::insert([
            [
                'level_id' => 1,
                'name' => 'A1',
                'sublevel_order' => 1,
                'total_hours' => 15,
                'default_session_duration' => 2,
                'max_capacity' => 10,
                'teacher_min_level' => 1,
                'price' => 800,
                'created_by_admin_id' => 1
            ],
            [
                'level_id' => 1,
                'name' => 'A2',
                'sublevel_order' => 2,
                'total_hours' => 15,
                'default_session_duration' => 2,
                'max_capacity' => 10,
                'teacher_min_level' => 1,
                'price' => 800,
                'created_by_admin_id' => 1
            ],
            [
                'level_id' => 2,
                'name' => 'B1',
                'sublevel_order' => 1,
                'total_hours' => 15,
                'default_session_duration' => 2,
                'max_capacity' => 10,
                'teacher_min_level' => 2,
                'price' => 900,
                'created_by_admin_id' => 1
            ],
            [
                'level_id' => 2,
                'name' => 'B2',
                'sublevel_order' => 2,
                'total_hours' => 15,
                'default_session_duration' => 2,
                'max_capacity' => 10,
                'teacher_min_level' => 2,
                'price' => 900,
                'created_by_admin_id' => 1
            ]
        ]);
    }
}
