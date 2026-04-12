<?php

namespace Database\Seeders\Enrollment;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Enrollment\Material;
use App\Models\Academic\CourseTemplate;
use App\Models\Academic\Level;
use App\Models\Academic\SubLevel;
use Illuminate\Support\Facades\DB;


class MaterialAssignmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $materials = Material::all();
        $courses = CourseTemplate::all();
        $levels = Level::all();
        $sublevels = SubLevel::all();

        foreach ($courses as $course) {
            DB::table('material_assignment')->insert([
                'material_id' => $materials->random()->material_id,
                'course_template_id' => $course->course_template_id ?? $courses->first()->course_template_id,
                'level_id' => $level?->level_id ?? $levels->first()->level_id,
                'sublevel_id' => $sublevel?->sublevel_id ?? $sublevels->first()->sublevel_id,
                'is_mandatory' => false
            ]);
        }

        foreach ($levels as $level) {
            DB::table('material_assignment')->insert([
                'material_id' => $materials->random()->material_id,
                'course_template_id' => null,
                'level_id' => $level->level_id,
                'sublevel_id' => null,
                'is_mandatory' => false
            ]);
        }
    }
}
