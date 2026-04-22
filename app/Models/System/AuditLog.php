<?php

namespace App\Models\System;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use App\Models\HR\Employee;

class AuditLog extends Model
{
    protected $table      = 'audit_log';
    protected $primaryKey = 'audit_log_id';
    public $timestamps    = true;

    protected $casts = [
        'record_id'  => 'integer',
        'changed_by' => 'integer',
        'changed_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    protected $fillable = [
        'table_name',
        'record_id',
        'field_name',
        'action_type',
        'old_value',
        'new_value',
        'changed_by',
        'changed_at',
    ];

    // ─────────────────────────────────────────
    // Relations
    // ─────────────────────────────────────────

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'changed_by', 'employee_id');
    }

    // ─────────────────────────────────────────
    // Helpers
    // ─────────────────────────────────────────

    public function isCreate(): bool { return $this->action_type === 'Create'; }
    public function isUpdate(): bool { return $this->action_type === 'Update'; }
    public function isDelete(): bool { return $this->action_type === 'Delete'; }

    public function changeSummary(): string
    {
        if ($this->isCreate()) return "Created record #{$this->record_id}";
        if ($this->isDelete()) return "Deleted record #{$this->record_id}";
        return "{$this->field_name}: {$this->old_value} → {$this->new_value}";
    }

    public function changeAge(): ?string
    {
        return $this->changed_at?->diffForHumans();
    }

    // ─────────────────────────────────────────
    // Scopes
    // ─────────────────────────────────────────

    public function scopeForTable(Builder $query, string $table): Builder
    {
        return $query->where('table_name', $table);
    }

    public function scopeForRecord(Builder $query, int $recordId): Builder
    {
        return $query->where('record_id', $recordId);
    }

    public function scopeByEmployee(Builder $query, int $employeeId): Builder
    {
        return $query->where('changed_by', $employeeId);
    }

    public function scopeCreated(Builder $query): Builder
    {
        return $query->where('action_type', 'Create');
    }

    public function scopeUpdated(Builder $query): Builder
    {
        return $query->where('action_type', 'Update');
    }

    public function scopeDeleted(Builder $query): Builder
    {
        return $query->where('action_type', 'Delete');
    }

    public function scopeToday(Builder $query): Builder
    {
        return $query->whereDate('changed_at', today());
    }
}