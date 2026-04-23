<?php

namespace Database\Seeders\Finance;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LevelPackageSeeder extends Seeder
{
    public function run(): void
    {
        $packages = [

            // ── General English (id: 1) ──
            // Level 1: 1500 LE, Level 2: 2000 LE → total individual = 3500 LE
            [
                'course_template_id' => 1,
                'name'               => '2 Levels Package',
                'levels_count'       => 2,
                'package_price'      => 3000.00, // saves 500 LE
                'is_active'          => true,
                'created_by_admin_id'=> 1,
                'created_at'         => now(),
                'updated_at'         => now(),
            ],

            // ── Conversation (id: 2) ──
            // Level 1: 1200 LE, Level 2: 1700 LE → total individual = 2900 LE
            [
                'course_template_id' => 2,
                'name'               => '2 Levels Package',
                'levels_count'       => 2,
                'package_price'      => 2500.00, // saves 400 LE
                'is_active'          => true,
                'created_by_admin_id'=> 1,
                'created_at'         => now(),
                'updated_at'         => now(),
            ],

        ];

        foreach ($packages as $package) {
            DB::table('level_package')->insertOrIgnore($package);
        }

        $this->command->info('LevelPackageSeeder: ' . count($packages) . ' packages seeded.');
    }
}