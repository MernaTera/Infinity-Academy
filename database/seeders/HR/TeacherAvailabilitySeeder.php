<?php

namespace Database\Seeders\HR;

use Illuminate\Database\Seeder;
use App\Models\HR\TeacherAvailability;
use App\Models\HR\Teacher;
use App\Models\Academic\TimeSlot;


class TeacherAvailabilitySeeder extends Seeder
{
    public function run(): void
    {
        $teachers = Teacher::all();
        $slots = TimeSlot::all();

        $days = ['sat_tue', 'sun_wed', 'mon_thu'];

        $data = [];

        foreach ($teachers as $teacher) {
            foreach ($days as $day) {
                $slot = $slots->random();

                $data[] = [
                    'teacher_id' => $teacher->teacher_id,
                    'time_slot_id' => $slot->time_slot_id,
                    'day_of_week' => $day,
                ];
            }
        }

        TeacherAvailability::insert($data);
    }
}