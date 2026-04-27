<?php

namespace Database\Seeders\Enrollment;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\HR\Employee;

class CsTargetSeeder extends Seeder
{
    public function run(): void
    {
        // جيبي كل الـ CS employees
        $csEmployees = Employee::whereHas('user.role', function ($q) {
            $q->where('role_name', 'Customer Service');
        })->get();

        if ($csEmployees->isEmpty()) {
            $this->command->warn('No CS employees found.');
            return;
        }

        $adminEmployee = Employee::whereHas('user.role', function ($q) {
            $q->where('role_name', 'Admin');
        })->first();

        if (!$adminEmployee) {
            $this->command->warn('No admin employee found.');
            return;
        }

        // الشهور اللي هتتضاف ليها targets
        $months = [
            now()->subMonths(2)->format('Y-m'),
            now()->subMonth()->format('Y-m'),
            now()->format('Y-m'),
            now()->addMonth()->format('Y-m'),
        ];

        $inserted = 0;

        foreach ($csEmployees as $cs) {
            foreach ($months as $month) {
                // تجنب الـ duplicates
                $exists = DB::table('cs_target')
                    ->where('employee_id', $cs->employee_id)
                    ->where('month', $month)
                    ->exists();

                if ($exists) continue;

                DB::table('cs_target')->insert([
                    'employee_id'          => $cs->employee_id,
                    'patch_id'             => null,
                    'month'                => $month,
                    'target_amount'        => 15000.00,
                    'target_registrations' => 10,
                    'is_locked'            => false,
                    'created_by_admin_id'  => $adminEmployee->employee_id,
                    'created_at'           => now(),
                    'updated_at'           => now(),
                ]);

                $inserted++;
            }
        }

        $this->command->info("CsTarget seeder done — {$inserted} records inserted.");
    }
}