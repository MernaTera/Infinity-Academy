<?php

namespace Database\Seeders\Academic;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Academic\CourseSession;

class CourseSessionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        CourseSession::insert([
            [
                'course_instance_id' => 1,
                'session_date' => '2026-01-07',
                'start_time' => '2026-01-07 09:00:00',
                'end_time' => '2026-01-07 11:00:00',
                'session_number' => 1,
                'room_id' => 1
            ]
        ]);
    }
}
