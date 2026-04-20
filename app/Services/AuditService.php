<?php

namespace App\Services;

use App\Models\System\AuditLog;
use App\Models\HR\Employee;

class AuditService
{
    public static function log(
        string $tableName,
        int    $recordId,
        string $fieldName,
        string $actionType,
        mixed  $oldValue = null,
        mixed  $newValue = null
    ): void {
        $employeeId = Employee::where('user_id', auth()->id())->first()?->employee_id;

        if (!$employeeId) return;

        AuditLog::create([
            'table_name'  => $tableName,
            'record_id'   => $recordId,
            'field_name'  => $fieldName,
            'action_type' => $actionType,
            'old_value'   => is_array($oldValue) ? json_encode($oldValue) : $oldValue,
            'new_value'   => is_array($newValue) ? json_encode($newValue) : $newValue,
            'changed_by'  => $employeeId,
        ]);
    }

    // ── Shortcuts ──
    public static function created(string $table, int $id, string $field = 'record', mixed $value = null): void
    {
        self::log($table, $id, $field, 'Create', null, $value);
    }

    public static function updated(string $table, int $id, string $field, mixed $old, mixed $new): void
    {
        if ($old == $new) return; 
        self::log($table, $id, $field, 'Update', $old, $new);
    }

    public static function deleted(string $table, int $id, string $field = 'record', mixed $value = null): void
    {
        self::log($table, $id, $field, 'Delete', $value, null);
    }
}