<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Enrollment
 * 
 * @property int $enrollment_id
 * @property int $student_id
 * @property int|null $placement_test_id
 * @property int|null $level_id
 * @property int|null $sublevel_id
 * @property int $course_instance_id
 * @property int $patch_id
 * @property int|null $branch_id
 * @property string|null $enrollment_type
 * @property string|null $delivery_mode
 * @property Carbon|null $preference_start_date
 * @property Carbon|null $actual_start_date
 * @property float|null $hours_remaining
 * @property float|null $final_price
 * @property int $payment_plan_id
 * @property int|null $bundle_id
 * @property float|null $discount_value
 * @property string|null $status
 * @property bool|null $restriction_flag
 * @property int|null $created_by_cs_id
 * @property Carbon|null $created_at
 * 
 * @property PrivateBundle|null $private_bundle
 * @property Employee|null $employee
 * @property CourseInstance $course_instance
 * @property Level|null $level
 * @property Patch $patch
 * @property Student $student
 * @property Sublevel|null $sublevel
 * @property PlacementTest|null $placement_test
 * @property Collection|Attendance[] $attendances
 * @property Collection|BundleUsageLog[] $bundle_usage_logs
 * @property Collection|FinancialTransaction[] $financial_transactions
 * @property Collection|InstallmentApprovalLog[] $installment_approval_logs
 * @property Collection|InstallmentSchedule[] $installment_schedules
 * @property Collection|Postponement[] $postponements
 * @property Collection|RefundRequest[] $refund_requests
 * @property Report|null $report
 * @property Collection|RestrictionLog[] $restriction_logs
 * @property Collection|WaitingList[] $waiting_lists
 *
 * @package App\Models
 */
class Enrollment extends Model
{
	protected $table = 'enrollment';
	protected $primaryKey = 'enrollment_id';
	public $timestamps = false;

	protected $casts = [
		'student_id' => 'int',
		'placement_test_id' => 'int',
		'level_id' => 'int',
		'sublevel_id' => 'int',
		'course_instance_id' => 'int',
		'patch_id' => 'int',
		'branch_id' => 'int',
		'preference_start_date' => 'datetime',
		'actual_start_date' => 'datetime',
		'hours_remaining' => 'float',
		'final_price' => 'float',
		'payment_plan_id' => 'int',
		'bundle_id' => 'int',
		'discount_value' => 'float',
		'restriction_flag' => 'bool',
		'created_by_cs_id' => 'int'
	];

	protected $fillable = [
		'student_id',
		'placement_test_id',
		'level_id',
		'sublevel_id',
		'course_instance_id',
		'patch_id',
		'branch_id',
		'enrollment_type',
		'delivery_mode',
		'preference_start_date',
		'actual_start_date',
		'hours_remaining',
		'final_price',
		'payment_plan_id',
		'bundle_id',
		'discount_value',
		'status',
		'restriction_flag',
		'created_by_cs_id'
	];

	public function private_bundle()
	{
		return $this->belongsTo(PrivateBundle::class, 'bundle_id');
	}

	public function employee()
	{
		return $this->belongsTo(Employee::class, 'created_by_cs_id');
	}

	public function course_instance()
	{
		return $this->belongsTo(CourseInstance::class);
	}

	public function level()
	{
		return $this->belongsTo(Level::class);
	}

	public function patch()
	{
		return $this->belongsTo(Patch::class);
	}

	public function student()
	{
		return $this->belongsTo(Student::class);
	}

	public function sublevel()
	{
		return $this->belongsTo(Sublevel::class);
	}

	public function placement_test()
	{
		return $this->belongsTo(PlacementTest::class);
	}

	public function attendances()
	{
		return $this->hasMany(Attendance::class);
	}

	public function bundle_usage_logs()
	{
		return $this->hasMany(BundleUsageLog::class);
	}

	public function financial_transactions()
	{
		return $this->hasMany(FinancialTransaction::class);
	}

	public function installment_approval_logs()
	{
		return $this->hasMany(InstallmentApprovalLog::class);
	}

	public function installment_schedules()
	{
		return $this->hasMany(InstallmentSchedule::class);
	}

	public function postponements()
	{
		return $this->hasMany(Postponement::class);
	}

	public function refund_requests()
	{
		return $this->hasMany(RefundRequest::class);
	}

	public function report()
	{
		return $this->hasOne(Report::class);
	}

	public function restriction_logs()
	{
		return $this->hasMany(RestrictionLog::class);
	}

	public function waiting_lists()
	{
		return $this->hasMany(WaitingList::class);
	}
}
