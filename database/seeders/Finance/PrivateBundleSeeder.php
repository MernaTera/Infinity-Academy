<?php

namespace Database\Seeders\Finance;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Finance\PrivateBundle;
use App\Models\HR\Employee;

class PrivateBundleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = Employee::first();

        PrivateBundle::insert([
            [
                'hours' => 20,
                'price' => 3000,
                'created_by_admin_id' => $admin->employee_id
            ],
            [
                'hours' => 40,
                'price' => 5500,
                'created_by_admin_id' => $admin->employee_id
            ]
        ]);
    }
}
