<?php


namespace App\Models\System;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use App\Models\HR\Employee;

/**
 * Class AuditLog
 * 
 * @property int $audit_log_id
 * @property string $table_name
 * @property int $record_id
 * @property string $field_name
 * @property string $action_type
 * @property string|null $old_value
 * @property string|null $new_value
 * @property int $changed_by
 * @property Carbon|null $changed_at
 * 
 * @property Employee $employee
 *
 * @package App\Models
 */
class AuditLog extends Model
{
	protected $table = 'audit_log';
	protected $primaryKey = 'audit_log_id';
	public $timestamps = true;

	protected $casts = [
		'record_id' => 'integer',
		'changed_by' => 'integer',
		'changed_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
	];

	protected $fillable = [
		'table_name',
		'record_id',
		'field_name',
		'action_type',
		'old_value',
		'new_value',
		'changed_by',
		'changed_at'
	];

	public function employee()
	{
		return $this->belongsTo(Employee::class, 'changed_by');
	}

    public function isCreate()
    {
        return $this->action_type === 'Create';
    }

    public function isUpdate()
    {
        return $this->action_type === 'Update';
    }

    public function isDelete()
    {
        return $this->action_type === 'Delete';
    }

    /*
    |--------------------------------------------------------------------------
    | Query Scopes
    |--------------------------------------------------------------------------
    */

    public function scopeForTable(Builder $query, $table)
    {
        return $query->where('table_name', $table);
    }

    public function scopeForRecord(Builder $query, $recordId)
    {
        return $query->where('record_id', $recordId);
    }

    public function scopeByEmployee(Builder $query, $employeeId)
    {
        return $query->where('changed_by', $employeeId);
    }

    public function scopeCreated(Builder $query)
    {
        return $query->where('action_type', 'Create');
    }

    public function scopeUpdated(Builder $query)
    {
        return $query->where('action_type', 'Update');
    }

    public function scopeDeleted(Builder $query)
    {
        return $query->where('action_type', 'Delete');
    }

    public function scopeToday(Builder $query)
    {
        return $query->whereDate('changed_at', today());
    }

    /*
    |--------------------------------------------------------------------------
    | Utilities
    |--------------------------------------------------------------------------
    */

    public function changeSummary()
    {
        return "{$this->field_name}: {$this->old_value} → {$this->new_value}";
    }

    public function changeAge()
    {
        if (!$this->changed_at) {
            return null;
        }

        return $this->changed_at->diffForHumans();
    }
}
