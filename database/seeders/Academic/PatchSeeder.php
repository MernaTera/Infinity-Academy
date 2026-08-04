<?php

namespace Database\Seeders\Academic;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Academic\Patch;

class PatchSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
    Patch::insert([
        [
            'name' => 'Patch july 2026',
            'branch_id' => 1,
            'start_date' => '2026-07-01',
            'end_date' => '2026-07-31',
            'status' => 'Closed',
            'created_by_admin_id' => 1
        ],
        [
            'name' => 'Patch August 2026',
            'branch_id' => 1,
            'start_date' => '2026-08-01',
            'end_date' => '2026-08-31',
            'status' => 'Active',
            'created_by_admin_id' => 1
        ],
        [
            'name' => 'Patch September 2026',
            'branch_id' => 1,
            'start_date' => '2026-09-01',
            'end_date' => '2026-09-30',
            'status' => 'Upcoming',
            'created_by_admin_id' => 1
        ]
    ]);
    }
}
