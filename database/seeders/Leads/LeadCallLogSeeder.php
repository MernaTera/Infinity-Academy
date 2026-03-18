<?php

namespace Database\Seeders\Leads;

use Illuminate\Database\Seeder;
use App\Models\Leads\LeadCallLog;
use App\Models\Leads\Lead;
use App\Models\HR\Employee;

class LeadCallLogSeeder extends Seeder
{
    public function run(): void
    {
        $leads = Lead::all();
        $employees = Employee::all();

        $data = [];

        foreach ($leads as $lead) {
            $callsCount = rand(1,3);

            for ($i = 0; $i < $callsCount; $i++) {
                $data[] = [
                    'lead_id' => $lead->lead_id,
                    'cs_id' => $employees->random()->employee_id,
                    'call_datetime' => now()->subDays(rand(0,5)),
                    'outcome' => fake()->randomElement([
                        'No_Answer','Interested','Not_Interested',
                        'Call_Again','Registered'
                    ]),
                    'notes' => fake()->sentence(),
                ];
            }
        }

        LeadCallLog::insert($data);
    }
}