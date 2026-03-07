<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

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
	public $timestamps = false;

	protected $casts = [
		'record_id' => 'int',
		'changed_by' => 'int',
		'changed_at' => 'datetime'
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
}
