<?php

namespace Database\Seeders\Academic;

use Illuminate\Database\Seeder;
use App\Models\Academic\BreakSlot;

class BreakSlotSeeder extends Seeder
{
    public function run(): void
    {
        BreakSlot::insert([
            [
                'name' => 'Break 1',
                'start_time' => '10:00:00',
                'end_time' => '10:15:00',
                'created_by_admin_id' => 1
            ],
            [
                'name' => 'Break 2',
                'start_time' => '12:00:00',
                'end_time' => '12:15:00',
                'created_by_admin_id' => 1
            ]
        ]);
    }
}