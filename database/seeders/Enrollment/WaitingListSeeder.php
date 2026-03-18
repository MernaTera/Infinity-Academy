<?php

namespace Database\Seeders\Enrollment;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Enrollment\WaitingList;

class WaitingListSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        WaitingList::insert([
            [
                'enrollment_id' => 1,
                'requested_patch_id' => 1,
                'preferred_type' => 'Next_Patch',
                'preferred_delivery_mood' => 'Offline',
                'status' => 'Active',
                'created_by_cs_id' => 1,
            ]
        ]);
    }
}
