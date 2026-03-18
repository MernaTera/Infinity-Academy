<?php

namespace Database\Seeders\Auth;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\Auth\User;
use App\Models\Auth\Role;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Roles
        $adminRole = Role::where('role_name', 'Admin')->first();
        $csRole = Role::where('role_name', 'Customer Service')->first();
        $teacherRole = Role::where('role_name', 'Teacher')->first();

        // Admin
        User::firstOrCreate([
            'email' => 'admin@infinity.com'
        ], [
            'name' => 'System Admin',
            'username' => 'admin',
            'password' => Hash::make('12345678'),
            'role_id' => $adminRole?->role_id,
            'is_active' => true
        ]);

        // Customer Service
        User::firstOrCreate([
            'email' => 'cs@infinity.com'
        ], [
            'name' => 'CS User',
            'username' => 'cs1',
            'password' => Hash::make('12345678'),
            'role_id' => $csRole?->role_id,
            'is_active' => true
        ]);

        // Teacher
        User::firstOrCreate([
            'email' => 'teacher@infinity.com'
        ], [
            'name' => 'Teacher User',
            'username' => 'teacher1',
            'password' => Hash::make('12345678'),
            'role_id' => $teacherRole?->role_id,
            'is_active' => true
        ]);
    }
}