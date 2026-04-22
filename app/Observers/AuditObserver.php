<?php

namespace App\Observers;

use App\Services\AuditLogger;
use Illuminate\Database\Eloquent\Model;

class AuditObserver
{
    /**
     * Tables that must NEVER be audited (prevents infinite loop).
     */
    protected array $excludedTables = [
        'audit_log',
        'sessions',
        'cache',
        'cache_locks',
        'jobs',
        'failed_jobs',
        'lead_history',   // has its own logging in LeadService
        'lead_call_log',  // has its own logging in LeadService
    ];

    protected array $skipFields = [
        'password',
        'remember_token',
        'updated_at',
        'created_at',
    ];

    // ─────────────────────────────────────────
    private function shouldAudit(Model $model): bool
    {
        return !in_array($model->getTable(), $this->excludedTables);
    }

    // ─────────────────────────────────────────
    // Created
    // ─────────────────────────────────────────
    public function created(Model $model): void
    {
        if (!$this->shouldAudit($model)) return;

        $table  = $model->getTable();
        $id     = $model->getKey();
        $values = collect($model->getAttributes())
            ->except($this->skipFields)
            ->toArray();

        AuditLogger::create($table, $id, $values);
    }

    // ─────────────────────────────────────────
    // Updated
    // ─────────────────────────────────────────
    public function updated(Model $model): void
    {
        if (!$this->shouldAudit($model)) return;

        $dirty = $model->getDirty();
        if (empty($dirty)) return;

        $table = $model->getTable();
        $id    = $model->getKey();

        $oldValues = collect($model->getOriginal())
            ->only(array_keys($dirty))
            ->except($this->skipFields)
            ->toArray();

        $newValues = collect($dirty)
            ->except($this->skipFields)
            ->toArray();

        if (empty($newValues)) return;

        AuditLogger::update($table, $id, $oldValues, $newValues);
    }

    // ─────────────────────────────────────────
    // Deleted
    // ─────────────────────────────────────────
    public function deleted(Model $model): void
    {
        if (!$this->shouldAudit($model)) return;

        $table  = $model->getTable();
        $id     = $model->getKey();
        $values = collect($model->getAttributes())
            ->except($this->skipFields)
            ->toArray();

        AuditLogger::delete($table, $id, $values);
    }
}