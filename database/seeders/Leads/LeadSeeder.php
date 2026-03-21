<?php

namespace Database\Seeders\Leads;

use Illuminate\Database\Seeder;
use App\Models\Leads\Lead;
use App\Models\Academic\CourseTemplate;
use App\Models\Academic\Level;
use App\Models\Academic\Sublevel;
use App\Models\HR\Employee;

class LeadSeeder extends Seeder
{
    public function run(): void
    {
        $templates = CourseTemplate::all();
        $levels = Level::all();
        $sublevels = Sublevel::all();
        $employees = Employee::all();

        $data = [];

        for ($i = 1; $i <= 20; $i++) {
            $template = $templates->random();
            $level = $levels->random();
            $sublevel = $sublevels->random();
            $cs = $employees->random();

            $data[] = [
                'full_name' => fake()->name(),
                'phone' => '010' . rand(10000000, 99999999),
                'birthdate' => fake()->date(),
                'location' => fake()->city(),
                'source' => fake()->randomElement(['Facebook','Website','Friend','Walk_In','Google']),
                'degree' => fake()->randomElement(['Student','Graduate']),
                'interested_course_template_id' => $template->course_template_id,
                'interested_level_id' => $level->level_id,
                'interested_sublevel_id' => $sublevel->sublevel_id,
                'status' => fake()->randomElement([
                    'Waiting','Call_Again','Scheduled_Call','Registered'
                ]),
                'start_preference_type' => fake()->randomElement([
                    'Current Patch','Next Patch','Specific Date'
                ]),
                'start_preference_date' => fake()->optional()->dateTimeBetween('+1 days', '+1 month'),
                'next_call_at' => now()->addDays(rand(1,5)),
                'owner_cs_id' => $cs->employee_id,
                'notes' => fake()->sentence(),
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        Lead::insert($data);
    }
}