<?php

namespace Database\Seeders\Academic;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Academic\EnglishLevel;

class EnglishLevelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        EnglishLevel::insert([
            ['level_name' => 'Beginner', 'level_rank' => 1],
            ['level_name' => 'Elementary', 'level_rank' => 2],
            ['level_name' => 'Intermediate', 'level_rank' => 3],
            ['level_name' => 'Advanced', 'level_rank' => 4],
        ]);
    }
}
