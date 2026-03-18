<?php

namespace Database\Seeders\Auth;

use Illuminate\Database\Seeder;
use App\Models\Auth\Module;
use App\Models\Auth\Permission;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        $actions = [
            'View',
            'Create',
            'Edit',
            'Delete',
            'Approve',
            'Override'
        ];

        $modules = Module::all();

        foreach ($modules as $module) {

            foreach ($actions as $action) {

                Permission::firstOrCreate([
                    'permission_key' => strtolower($module->module_name) . '.' . strtolower($action)
                ],[
                    'module_id' => $module->module_id,
                    'action_type' => $action
                ]);

            }

        }
    }
}