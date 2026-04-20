<?php

namespace Database\Seeders\Academic;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BreakSlotSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('break_slot')->insert([
            [
                'name' => 'Mid-Morning Break',
                'start_time' => '11:00:00',
                'end_time' => '12:00:00',
                'is_active' => 1,
                'created_by_admin_id' => 1,


            ],
            [
                'name' => 'Afternoon Break',
                'start_time' => '14:00:00',
                'end_time' => '18:00:00',
                'is_active' => 1,
                'created_by_admin_id' => 1,

            ],
        ]);
    }
}