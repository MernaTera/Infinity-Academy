<?php

namespace Database\Seeders\HR;

use Illuminate\Database\Seeder;
use App\Models\HR\Employee;
use App\Models\Auth\User;
use App\Models\Core\Branch;

class EmployeeSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::all();
        $branch = Branch::first();

        $data = [];

        foreach ($users as $user) {
            $data[] = [
                'full_name' => $user->name,
                'user_id' => $user->id,
                'branch_id' => $branch->branch_id,
                'salary' => rand(5000, 15000),
                'status' => 'Active',
                'hired_at' => now(),
            ];
        }

        Employee::insert($data);
    }
}