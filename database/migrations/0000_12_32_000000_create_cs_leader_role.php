<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Add the "CS Leader" role.
 *
 * A CS Leader is a normal Customer Service user with team-oversight extras
 * (team sales dashboard + all-branch leads). They get EXACTLY the Customer
 * Service permission set here, so every CS route/action works for them; the
 * extra leader-only views are gated separately by the `cs.leader` middleware.
 *
 * Idempotent: safe to run more than once.
 */
return new class extends Migration {
    public function up(): void
    {
        // 1) Create the role if it doesn't exist.
        $leaderId = DB::table('role')->where('role_name', 'CS Leader')->value('role_id');
        if (!$leaderId) {
            $leaderId = DB::table('role')->insertGetId([
                'role_name'  => 'CS Leader',
                'is_active'  => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // 2) Give it the same permissions as Customer Service.
        $csId = DB::table('role')->where('role_name', 'Customer Service')->value('role_id');
        if ($csId) {
            $csPermissionIds = DB::table('role_permission')
                ->where('role_id', $csId)
                ->pluck('permission_id');

            foreach ($csPermissionIds as $permissionId) {
                $exists = DB::table('role_permission')
                    ->where('role_id', $leaderId)
                    ->where('permission_id', $permissionId)
                    ->exists();

                if (!$exists) {
                    DB::table('role_permission')->insert([
                        'role_id'       => $leaderId,
                        'permission_id' => $permissionId,
                    ]);
                }
            }
        }
    }

    public function down(): void
    {
        $leaderId = DB::table('role')->where('role_name', 'CS Leader')->value('role_id');
        if ($leaderId) {
            DB::table('role_permission')->where('role_id', $leaderId)->delete();
            // Only remove the role if no employees/users are still assigned to it.
            $inUse = DB::table('users')->where('role_id', $leaderId)->exists();
            if (!$inUse) {
                DB::table('role')->where('role_id', $leaderId)->delete();
            }
        }
    }
};
