<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Auth\Role;
use App\Models\Auth\Permission;
use App\Models\Auth\RolePermission;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {

        $rolePermissions = [

            'Admin' => Permission::pluck('permission_id')->toArray(),

            'Customer Service' => [
                'leads.view',
                'leads.create',
                'leads.edit',
                'leads.delete',
            ],

            'Student Care' => [
                'enrollment.view',
                'enrollment.create',
                'enrollment.edit',
                'academic.view',
            ],

            'Teacher' => [
                'academic.view',
                'attendance.view',
                'attendance.edit',
                'reports.create',
            ],

            'Student' => [
                'academic.view',
                'attendance.view',
            ],
        ];

        foreach ($rolePermissions as $roleName => $permissions) {

            $role = Role::where('role_name', $roleName)->first();

            if (!$role) continue;

            // Admin case
            if ($roleName === 'Admin') {

                foreach ($permissions as $permissionId) {

                    RolePermission::firstOrCreate([
                        'role_id' => $role->role_id,
                        'permission_id' => $permissionId
                    ]);

                }

                continue;
            }

            // Other roles
            foreach ($permissions as $permissionKey) {

                $permission = Permission::where('permission_key', $permissionKey)->first();

                if (!$permission) continue;

                RolePermission::firstOrCreate([
                    'role_id' => $role->role_id,
                    'permission_id' => $permission->permission_id
                ]);

            }

        }

    }
}