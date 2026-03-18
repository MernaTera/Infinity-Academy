<?php

namespace Database\Seeders\Enrollment;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Enrollment\Postponement;

class PostponementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Postponement::insert([
            [
                'enrollment_id' => 1,
                'start_date' => now(),
                'expected_return_date' => now()->addDays(7),
                'status' => 'Active',
                'created_by_cs_id' => 1,
            ]
        ]);
    }
}
