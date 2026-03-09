<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use App\Models\Core\Branch;
use App\Models\Academic\CourseInstance;
use App\Models\Academic\Level;
use App\Models\Academic\Patch;
use App\Models\Student\Student;
use App\Models\Academic\Sublevel;
use App\Models\Enrollment\PlacementTest;
use App\Models\HR\Employee;
use App\Models\Attendance\Attendance;
use App\Models\Finance\BundleUsageLog;
use App\Models\Finance\FinancialTransaction;
use App\Models\Finance\InstallmentApprovalLog;
use App\Models\Finance\InstallmentSchedule;
use App\Models\Enrollment\Postponement;
use App\Models\Finance\RefundRequest;
use App\Models\Reports\Report;
use App\Models\Enrollment\RestrictionLog;
use App\Models\Enrollment\WaitingList;

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
		'student_id' => 'integer',
		'placement_test_id' => 'integer',
		'level_id' => 'integer',
		'sublevel_id' => 'integer',
		'course_instance_id' => 'integer',
		'patch_id' => 'integer',
		'branch_id' => 'integer',
		'preference_start_date' => 'date',
		'actual_start_date' => 'date',
		'hours_remaining' => 'decimal:2',
		'final_price' => 'decimal:2',
		'payment_plan_id' => 'integer',
		'bundle_id' => 'integer',
		'discount_value' => 'decimal:2',
		'restriction_flag' => 'boolean',
		'created_by_cs_id' => 'integer',
		'created_at' => 'datetime'
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

	public function privateBundle()
	{
		return $this->belongsTo(PrivateBundle::class, 'bundle_id');
	}

	public function employee()
	{
		return $this->belongsTo(Employee::class, 'created_by_cs_id');
	}

	public function courseInstance()
	{
		return $this->belongsTo(CourseInstance::class, 'course_instance_id');
	}

	public function level()
	{
		return $this->belongsTo(Level::class, 'level_id');
	}

	public function patch()
	{
		return $this->belongsTo(Patch::class);
	}

	public function student()
	{
		return $this->belongsTo(Student::class, 'student_id');
	}

	public function sublevel()
	{
		return $this->belongsTo(Sublevel::class, 'sublevel_id');
	}

	public function placementTest()
	{
		return $this->belongsTo(PlacementTest::class, 'placement_test_id');
	}

	public function attendances()
	{
		return $this->hasMany(Attendance::class, 'enrollment_id');
	}

	public function bundleUsageLogs()
	{
		return $this->hasMany(BundleUsageLog::class, 'enrollment_id');
	}

	public function financialTransactions()
	{
		return $this->hasMany(FinancialTransaction::class, 'enrollment_id');
	}

	public function installmentApprovalLogs()
	{
		return $this->hasMany(InstallmentApprovalLog::class, 'enrollment_id');
	}

	public function installmentSchedules()
	{
		return $this->hasMany(InstallmentSchedule::class, 'enrollment_id');
	}

	public function postponements()
	{
		return $this->hasMany(Postponement::class, 'enrollment_id');
	}

	public function refundRequests()
	{
		return $this->hasMany(RefundRequest::class, 'enrollment_id');
	}

	public function report()
	{
		return $this->hasOne(Report::class, 'enrollment_id');
	}

	public function restrictionLogs()
	{
		return $this->hasMany(RestrictionLog::class, 'enrollment_id');
	}

	public function waitingLists()
	{
		return $this->hasMany(WaitingList::class, 'enrollment_id');
	}

	public function isActive()
	{
		return $this->status === 'Active';
	}

	public function isRestricted()
	{
		return $this->restriction_flag === true;
	}

	public function isPrivate()
	{
		return $this->enrollment_type === 'private';
	}

	public function isGroup()
	{
		return $this->enrollment_type === 'group';
	}

	public function isCompleted()
	{
		return $this->status === 'completed';
	}

	public function isCancelled()
	{
		return $this->status === 'cancelled';
	}

	public function totalPaid()
	{
		return $this->financialTransactions()
			->where('transaction_type', 'payment')
			->sum('amount');
	}

	public function totalRefunded()
	{
		return $this->financialTransactions()
			->where('transaction_type', 'refund')
			->sum('amount');
	}

	public function totalDue()
	{
		return max(0, $this->final_price - $this->totalPaid() + $this->totalRefunded());		
	}

	public function remainingSessions()
	{
		if ($this->courseInstance && $this->courseInstance->totalSessions() > 0) {
			$totalSessions = $this->courseInstance->totalSessions();
			$attendedSessions = $this->attendances()->where('status', 'attended')->count();
			return max(0, $totalSessions - $attendedSessions);
		}
		return null;
	}

	public function remainingAmount()
	{
		return max(0, $this->final_price - $this->totalPaid() + $this->totalRefunded());
	}

	public function attendedSessions()
	{
		return $this->attendances()->where('status', 'Present')->count();
	}

	public function absences()
	{
		return $this->attendances()->where('status', 'Absent')->count();
	}

	public function hasOverdueInstallments()
	{
		return $this->installmentSchedules()
			->where('status', 'Overdue')
			->exists();
	}

	public function hasUpcomingInstallments()
	{
		return $this->installmentSchedules()
			->where('status', 'Scheduled')
			->where('due_date', '>', Carbon::now())
			->exists();
	}

}
