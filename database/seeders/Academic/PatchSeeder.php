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
            'name' => 'Patch Jan 2026',
            'branch_id' => 1,
            'start_date' => '2026-01-01',
            'end_date' => '2026-05-04',
            'status' => 'Closed',
            'created_by_admin_id' => 1
        ],
        [
            'name' => 'Patch July-August 2026',
            'branch_id' => 1,
            'start_date' => '2026-07-21',
            'end_date' => '2026-08-21',
            'status' => 'Active',
            'created_by_admin_id' => 1
        ],
        [
            'name' => 'Patch September 2026',
            'branch_id' => 1,
            'start_date' => '2026-09-22',
            'end_date' => '2026-10-23',
            'status' => 'Upcoming',
            'created_by_admin_id' => 1
        ]
    ]);
    }
}
