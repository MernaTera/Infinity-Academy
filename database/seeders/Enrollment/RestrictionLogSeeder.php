<?php

namespace Database\Seeders\Enrollment;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Enrollment\RestrictionLog;

class RestrictionLogSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        RestrictionLog::insert([
            [
                'enrollment_id' => 1,
                'triggered_by' => 'Customer_Service',
                'reason' => 'absence_limit_exceeded',
                'notes' => 'Too many absences',
            ]
        ]);
    }
}
