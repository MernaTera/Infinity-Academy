<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Auth\Module;

class ModuleSeeder extends Seeder
{
    public function run(): void
    {
        $modules = [
            'Leads',
            'Enrollment',
            'Academic',
            'Attendance',
            'Financial',
            'Reports',
            'HR',
            'Notifications'
        ];

        foreach ($modules as $module) {
            Module::firstOrCreate([
                'module_name' => $module
            ]);
        }
    }
}