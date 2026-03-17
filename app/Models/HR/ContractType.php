<?php


namespace App\Models\HR;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use App\Models\HR\Employee;
use App\Models\Academic\Patch;
use App\Models\HR\Teacher;

/**
 * Class ContractType
 * 
 * @property int $contract_id
 * @property int $teacher_id
 * @property int $patch_id
 * @property string $contract_type
 * @property int $max_sessions_allowed
 * @property bool|null $is_active
 * @property int $created_by_admin_id
 * @property Carbon|null $created_at
 * 
 * @property Employee $employee
 * @property Patch $patch
 * @property Teacher $teacher
 *
 * @package App\Models
 */
class ContractType extends Model
{
	protected $table = 'contract_type';
	protected $primaryKey = 'contract_id';
	public $timestamps = true;

	protected $casts = [
		'teacher_id' => 'integer',
		'patch_id' => 'integer',
		'max_sessions_allowed' => 'integer',
		'is_active' => 'boolean',
		'created_by_admin_id' => 'integer',
		'created_at' => 'datetime'
	];

	protected $fillable = [
		'teacher_id',
		'patch_id',
		'contract_type',
		'max_sessions_allowed',
		'is_active',
		'created_by_admin_id'
	];

	public function createdByAdmin()
	{
		return $this->belongsTo(Employee::class, 'created_by_admin_id');
	}

	public function patch()
	{
		return $this->belongsTo(Patch::class, 'patch_id');
	}

	public function teacher()
	{
		return $this->belongsTo(Teacher::class, 'teacher_id');
	}

	public function isActive()
	{
		return $this->is_active;
	}

	public function reachedLimit($sessionsCount)
	{
		return $sessionsCount >= $this->max_sessions_allowed;
	}

	public function scopeForPatch($query, $patchId)
	{
		return $query->where('patch_id', $patchId);
	}
}
