<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

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
