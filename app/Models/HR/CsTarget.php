<?php


namespace App\Models\HR;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use App\Models\HR\Employee;
use App\Models\HR\Patch;

/**
 * Class CsTarget
 * 
 * @property int $target_id
 * @property int $employee_id
 * @property int $patch_id
 * @property float|null $target_amount
 * @property int|null $target_registrations
 * @property bool|null $is_locked
 * @property int $created_by_admin_id
 * @property Carbon|null $created_at
 * 
 * @property Employee $employee
 * @property Patch $patch
 *
 * @package App\Models
 */
class CsTarget extends Model
{
	protected $table = 'cs_target';
	protected $primaryKey = 'target_id';
	public $timestamps = false;

	protected $casts = [
		'employee_id' => 'integer',
		'patch_id' => 'integer',
		'target_amount' => 'float',
		'target_registrations' => 'integer',
		'is_locked' => 'boolean',
		'created_by_admin_id' => 'integer',
		'created_at' => 'datetime'
	];

	protected $fillable = [
		'employee_id',
		'patch_id',
		'target_amount',
		'target_registrations',
		'is_locked',
		'created_by_admin_id'
	];

	public function employee()
	{
		return $this->belongsTo(Employee::class, 'employee_id');
	}

	public function patch()
	{
		return $this->belongsTo(Patch::class, 'patch_id');
	}

	public function createdByAdmin()
	{
		return $this->belongsTo(Employee::class, 'created_by_admin_id');
	}

	public function isLocked()
	{
		return $this->is_locked;
	}

	public function remainingAmount($currentRevenue)
	{
		return max(0, $this->target_amount - $currentRevenue);
	}

	public function scopeForPatch($query, $patchId)
	{
		return $query->where('patch_id', $patchId);
	}
	
}
