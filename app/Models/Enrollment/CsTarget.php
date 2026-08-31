<?php


namespace App\Models\Enrollment;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use App\Models\HR\Employee;
use App\Models\Academic\Patch;

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
	public $timestamps = true;

	protected $casts = [
		'employee_id' => 'integer',
		'patch_id' => 'integer',
		'target_amount' => 'decimal:2',
		'target_registrations' => 'integer',
		'is_locked' => 'boolean',
		'created_by_admin_id' => 'integer',
		'created_at' => 'datetime'
	];

	protected $fillable = [
		'employee_id',
		'patch_id',
		'month',
		'target_amount',
		'target_registrations',
		'is_locked',
		'created_by_admin_id'
	];

	public function employee()
	{
		return $this->belongsTo(Employee::class, 'employee_id');
	}

	public function scopeForMonth($query, string $month)
	{
		return $query->where('month', $month);
	}

	/**
	 * The standing (permanent) target amount for a CS employee.
	 *
	 * The target is set once when the CS is created and applies to EVERY month
	 * until an admin changes it — it is not per-month. It's stored on the row
	 * with month = NULL. For backward compatibility with older data that saved
	 * a month-specific row, we fall back to the most recent month row when no
	 * standing (NULL) row exists yet.
	 */
	public static function amountFor($employeeId): float
	{
		if (!$employeeId) return 0.0;

		$standing = static::where('employee_id', $employeeId)
			->whereNull('month')
			->value('target_amount');

		if ($standing !== null) return (float) $standing;

		// Legacy fallback: newest month-specific target, if any.
		$legacy = static::where('employee_id', $employeeId)
			->whereNotNull('month')
			->orderByDesc('month')
			->value('target_amount');

		return (float) ($legacy ?? 0);
	}

	/** Set/update the standing (permanent) target for a CS employee. */
	public static function setStanding($employeeId, $amount, $adminId = null): void
	{
		static::updateOrCreate(
			['employee_id' => $employeeId, 'month' => null],
			[
				'target_amount'       => $amount,
				'is_locked'           => false,
				'created_by_admin_id' => $adminId,
			]
		);
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
