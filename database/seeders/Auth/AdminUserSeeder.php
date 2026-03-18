<?php

namespace Database\Seeders\Auth;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\Auth\User;
use App\Models\Auth\Role;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $adminRole = Role::where('role_name','Admin')->first();

        User::firstOrCreate([
            'email' => 'admin@infinity.com'
        ],[
            'name' => 'System Admin',
            'username' => 'admin',
            'password' => Hash::make('12345678'),
            'role_id' => $adminRole->role_id,
            'is_active' => true
        ]);
    }
}