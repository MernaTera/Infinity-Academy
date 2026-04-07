<?php

namespace Database\Seeders\Academic;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Academic\CourseTemplate;

class CourseTemplateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        CourseTemplate::updateOrCreate(
            ['name' => 'General English'],
            [
                'price' => 3000,
                'private_allowed' => 1,
                'private_only' => 0,
                'created_by_admin_id' => 1
            ]
        );

        CourseTemplate::updateOrCreate(
            ['name' => 'Conversation'],
            [
                'price' => 2500,
                'private_allowed' => 1,
                'private_only' => 0,
                'created_by_admin_id' => 1
            ]
        );
    }
}
