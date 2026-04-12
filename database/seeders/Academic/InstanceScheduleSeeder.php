<?php

namespace Database\Seeders\Academic;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Academic\InstanceSchedule;

class InstanceScheduleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        InstanceSchedule::insert([
            [
                'course_instance_id' => 1,
                'day_of_week' => 'sun_wed',
                'time_slot_id' => 1,
                'created_by_employee_id' => 1
            ],
            [
                'course_instance_id' => 1,
                'day_of_week' => 'sat_tue',
                'time_slot_id' => 1,
                'created_by_employee_id' => 1
            ],
            [
                'course_instance_id' => 2,
                'day_of_week' => 'mon_thu',
                'time_slot_id' => 2,
                'created_by_employee_id' => 1
            ],
            [
                'course_instance_id' => 2,
                'day_of_week' => 'sun_wed',
                'time_slot_id' => 2,
                'created_by_employee_id' => 1
            ]
        ]);
            }
}
