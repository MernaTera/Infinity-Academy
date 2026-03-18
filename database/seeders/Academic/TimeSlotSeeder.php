<?php

namespace Database\Seeders\Academic;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Academic\TimeSlot;

class TimeSlotSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        TimeSlot::insert([
            [
                'name' => 'Morning Slot',
                'start_time' => '09:00:00',
                'end_time' => '11:00:00',
                'slot_type' => 'Morning',
                'created_by_admin_id' => 1
            ],
            [
                'name' => 'Midday Slot',
                'start_time' => '12:00:00',
                'end_time' => '14:00:00',
                'slot_type' => 'Midday',
                'created_by_admin_id' => 1
            ],
            [
                'name' => 'Night Slot',
                'start_time' => '18:00:00',
                'end_time' => '20:00:00',
                'slot_type' => 'Night',
                'created_by_admin_id' => 1
            ]
        ]);
    }
}
