<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
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
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Afternoon Break',
                'start_time' => '14:00:00',
                'end_time' => '18:00:00',
                'is_active' => 1,
                'created_by_admin_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    public function down(): void
    {
        DB::table('break_slot')->truncate();
    }
}