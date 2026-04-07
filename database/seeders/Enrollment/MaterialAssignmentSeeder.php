<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MaterialAssignmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
DB::table('material_assignment')->insert([
    [
        'material_id' => 1,
        'course_template_id' => 1,
        'is_mandatory' => false
    ],
    [
        'material_id' => 2,
        'level_id' => 1,
        'is_mandatory' => false
    ]
]);
    }
}
