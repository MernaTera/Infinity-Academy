<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Auth\Role;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        Role::create([
            'role_name' => 'Admin',
            'is_active' => true
        ]);

        Role::create([
            'role_name' => 'Customer Service',
            'is_active' => true
        ]);

        Role::create([
            'role_name' => 'Student Care',
            'is_active' => true
        ]);

        Role::create([
            'role_name' => 'Teacher',
            'is_active' => true
        ]);

        Role::create([
            'role_name' => 'Student',
            'is_active' => true
        ]);
    }
}
