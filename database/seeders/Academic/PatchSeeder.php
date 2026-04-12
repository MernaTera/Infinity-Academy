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
            'end_date' => '2026-03-01',
            'status' => 'Closed',
            'created_by_admin_id' => 1
        ],
        [
            'name' => 'Patch Feb 2026',
            'branch_id' => 1,
            'start_date' => '2026-03-02',
            'end_date' => '2026-05-02',
            'status' => 'Active',
            'created_by_admin_id' => 1
        ],
        [
            'name' => 'Patch Mar 2026',
            'branch_id' => 1,
            'start_date' => '2026-05-03',
            'end_date' => '2026-07-03',
            'status' => 'Upcoming',
            'created_by_admin_id' => 1
        ]
    ]);
    }
}
