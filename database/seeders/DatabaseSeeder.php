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
    }
}
