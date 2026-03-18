<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Database\Seeders\Auth\RoleSeeder;
use Database\Seeders\Auth\ModuleSeeder;
use Database\Seeders\Auth\PermissionSeeder;
use Database\Seeders\Auth\RolePermissionSeeder;
use Database\Seeders\Auth\AdminUserSeeder;
use Database\Seeders\Auth\UserSeeder;

use Database\Seeders\Core\BranchSeeder;

use Database\Seeders\Academic\EnglishLevelSeeder;
use Database\Seeders\Academic\CourseTemplateSeeder;
use Database\Seeders\Academic\CourseSessionSeeder;
use Database\Seeders\Academic\BreakSlotSeeder;
use Database\Seeders\Academic\TimeSlotSeeder;
use Database\Seeders\Academic\CourseInstanceSeeder;
use Database\Seeders\Academic\InstanceScheduleSeeder;
use Database\Seeders\Academic\PatchSeeder;
use Database\Seeders\Academic\SublevelSeeder;
use Database\Seeders\Academic\LevelSeeder;
use Database\Seeders\Academic\RoomSeeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            \Database\Seeders\Auth\RoleSeeder::class,
            \Database\Seeders\Auth\ModuleSeeder::class,
            \Database\Seeders\Auth\PermissionSeeder::class,
            \Database\Seeders\Auth\RolePermissionSeeder::class,
            \Database\Seeders\Auth\AdminUserSeeder::class,
            \Database\Seeders\Auth\UserSeeder::class
        ]);

        $this->call([
            \Database\Seeders\Core\BranchSeeder::class,
        ]);

        $this->call([
            \Database\Seeders\Academic\EnglishLevelSeeder::class,
            \Database\Seeders\Academic\CourseTemplateSeeder::class,
            \Database\Seeders\Academic\CourseSessionSeeder::class,
            \Database\Seeders\Academic\BreakSlotSeeder::class,
            \Database\Seeders\Academic\TimeSlotSeeder::class,
            \Database\Seeders\Academic\CourseInstanceSeeder::class,
            \Database\Seeders\Academic\InstanceScheduleSeeder::class,
            \Database\Seeders\Academic\PatchSeeder::class,
            \Database\Seeders\Academic\SublevelSeeder::class,
            \Database\Seeders\Academic\LevelSeeder::class,
            \Database\Seeders\Academic\RoomSeeder::class,
        ]);
    }
}
