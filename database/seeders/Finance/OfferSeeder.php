<?php

namespace Database\Seeders\Finance;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Finance\Offer;
use App\Models\Academic\CourseTemplate;
use App\Models\HR\Employee;

class OfferSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = Employee::first();
        $template = CourseTemplate::first();

        $offer = Offer::create([
            'offer_name' => 'Summer Discount',
            'discount_type' => 'Percentage',
            'discount_value' => 20,
            'start_date' => now(),
            'end_date' => now()->addMonth(),
            'created_by_admin_id' => $admin->employee_id
        ]);

        $offer->courseTemplates()->attach($template->course_template_id);
    }
}
