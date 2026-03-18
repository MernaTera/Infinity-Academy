<?php

namespace Database\Seeders\Leads;

use Illuminate\Database\Seeder;
use App\Models\Leads\LeadHistory;
use App\Models\Leads\Lead;
use App\Models\HR\Employee;

class LeadHistorySeeder extends Seeder
{
    public function run(): void
    {
        $leads = Lead::all();
        $employees = Employee::all();

        $statuses = [
            'Waiting','Call_Again','Scheduled_Call',
            'Registered','Not_Interested','Archived'
        ];

        $data = [];

        foreach ($leads as $lead) {
            $old = $statuses[array_rand($statuses)];
            $new = $statuses[array_rand($statuses)];

            $data[] = [
                'lead_id' => $lead->lead_id,
                'old_status' => $old,
                'new_status' => $new,
                'notes' => fake()->sentence(),
                'changed_by' => $employees->random()->employee_id,
                'changed_at' => now(),
            ];
        }

        LeadHistory::insert($data);
    }
}