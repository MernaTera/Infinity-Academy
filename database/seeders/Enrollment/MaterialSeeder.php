<?php

namespace Database\Seeders\Enrollment;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Enrollment\Material;

class MaterialSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Material::updateOrCreate(
            ['name' => 'Grammar book A'],
            ['price' => 200, 'created_by_admin_id' => 1]
        );

        Material::updateOrCreate(
            ['name' => 'Workbook A'],
            ['price' => 150, 'created_by_admin_id' => 1]
        );
    }
}
