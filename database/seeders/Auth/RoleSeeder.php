<?php

namespace Database\Seeders\Auth;

use Illuminate\Database\Seeder;
use App\Models\Auth\Role;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            ['role_name' => 'Admin',            'is_active' => true],
            ['role_name' => 'Customer Service', 'is_active' => true],
            ['role_name' => 'Student Care',     'is_active' => true],
            ['role_name' => 'Teacher',          'is_active' => true],
            ['role_name' => 'Student',          'is_active' => true],
        ];

        foreach ($roles as $role) {
            Role::firstOrCreate(
                ['role_name' => $role['role_name']],
                ['is_active' => $role['is_active']]
            );
        }
    }
}