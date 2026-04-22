<?php

namespace App\Services;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AuditLogger
{
    /**
     * Log a CREATE action.
     */
    public static function create(string $table, int $recordId, array $newValues = []): void
    {
        if (empty($newValues)) {
            self::write($table, $recordId, 'Create', 'record', null, 'created');
            return;
        }

        foreach ($newValues as $field => $value) {
            self::write($table, $recordId, 'Create', $field, null, $value);
        }
    }

    /**
     * Log an UPDATE action — only changed fields.
     */
    public static function update(string $table, int $recordId, array $oldValues, array $newValues): void
    {
        $skip = ['updated_at', 'created_at', 'remember_token'];

        foreach ($newValues as $field => $newValue) {
            if (in_array($field, $skip)) continue;

            $oldValue = $oldValues[$field] ?? null;
            if ((string) $oldValue === (string) $newValue) continue;

            self::write($table, $recordId, 'Update', $field, $oldValue, $newValue);
        }
    }

    /**
     * Log a DELETE action.
     */
    public static function delete(string $table, int $recordId, array $oldValues = []): void
    {
        self::write($table, $recordId, 'Delete', 'record', json_encode($oldValues), null);
    }

    /**
     * Raw writer — uses DB::table() directly to bypass Eloquent observers
     * and prevent infinite loop.
     */
    public static function write(
        string $table,
        int    $recordId,
        string $actionType,
        string $fieldName,
        mixed  $oldValue,
        mixed  $newValue
    ): void {
        try {
            $employeeId = Auth::check()
                ? Auth::user()->employee?->employee_id
                : null;


            DB::table('audit_log')->insert([
                'table_name'  => $table,
                'record_id'   => $recordId,
                'field_name'  => $fieldName,
                'action_type' => $actionType,
                'old_value'   => is_array($oldValue) ? json_encode($oldValue) : (string) ($oldValue ?? ''),
                'new_value'   => is_array($newValue) ? json_encode($newValue) : (string) ($newValue ?? ''),
                'changed_by'  => $employeeId,
                'changed_at'  => now(),
                'created_at'  => now(),
                'updated_at'  => now(),
            ]);
        } catch (\Exception $e) {
            \Log::error('AuditLogger failed: ' . $e->getMessage());
        }
    }
}