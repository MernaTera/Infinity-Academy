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
            'name' => 'Patch Feb 2026',
            'branch_id' => 1,
            'start_date' => '2026-07-05',
            'end_date' => '2026-08-06',
            'status' => 'Active',
            'created_by_admin_id' => 1
        ],
        [
            'name' => 'Patch Mar 2026',
            'branch_id' => 1,
            'start_date' => '2026-08-07',
            'end_date' => '2026-09-08',
            'status' => 'Upcoming',
            'created_by_admin_id' => 1
        ]
    ]);
    }
}
