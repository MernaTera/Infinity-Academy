<?php

namespace Database\Seeders\Enrollment;

use Illuminate\Database\Seeder;
use App\Models\Enrollment\WaitingList;
use App\Models\Enrollment\Enrollment;
use App\Models\Academic\Patch;
use App\Models\HR\Employee;

class WaitingListSeeder extends Seeder
{
    public function run(): void
    {
        $enrollment = Enrollment::first();
        $patch = Patch::first();
        $employee = Employee::first();

        if (!$enrollment || !$patch) {
            return;
        }

        WaitingList::create([
            'enrollment_id' => $enrollment->enrollment_id,
            'requested_patch_id' => $patch->patch_id,
            'preferred_type' => 'Next_Patch',
            'preferred_delivery_mood' => 'Offline',
            'status' => 'Active',
            'created_by_cs_id' => $employee?->employee_id,
        ]);
    }
}
