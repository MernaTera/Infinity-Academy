<?php


namespace App\Models\Finance;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use App\Models\HR\Employee;
use App\Models\Enrollment\Enrollment;
use App\Models\Academic\CourseSession;

/**
 * Class BundleUsageLog
 * 
 * @property int $usage_id
 * @property int $enrollment_id
 * @property int $session_id
 * @property float $hours_deducted
 * @property string|null $reason
 * @property int|null $created_by_cs_id
 * @property Carbon|null $created_at
 * 
 * @property Employee|null $employee
 * @property Enrollment $enrollment
 * @property Session $session
 *
 * @package App\Models
 */
class BundleUsageLog extends Model
{
	protected $table = 'bundle_usage_log';
	protected $primaryKey = 'usage_id';
	public $timestamps = false;

	protected $casts = [
		'enrollment_id' => 'integer',
		'session_id' => 'integer',
		'hours_deducted' => 'decimal:2',
		'created_by_cs_id' => 'integer',
		'created_at' => 'datetime'
	];

	protected $fillable = [
		'enrollment_id',
		'session_id',
		'hours_deducted',
		'reason',
		'created_by_cs_id'
	];

	public function createdBy()
	{
		return $this->belongsTo(Employee::class, 'created_by_cs_id');
	}

	public function enrollment()
	{
		return $this->belongsTo(Enrollment::class, 'enrollment_id');
	}

	public function session()
	{
		return $this->belongsTo(Session::class, 'session_id');
	}

	public function isAttendanceDeduction()
	{
		return $this->reason === 'ATTENDANCE';
	}

	public function isManualAdjustment()
	{
		return $this->reason === 'MANUAL_ADJUSTMENT';
	}

	public function isOtherReason()
	{
		return $this->reason !== 'ATTENDANCE' && $this->reason !== 'MANUAL_ADJUSTMENT';
	}

	public function remainingBundleHours()
	{
		$used = $this->bundleUsageLogs()->sum('hours_deducted');
		return $this->hours_remaining - $used;
	}

}
