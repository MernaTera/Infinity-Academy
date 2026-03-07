<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Employee
 * 
 * @property int $employee_id
 * @property string $full_name
 * @property int|null $user_id
 * @property int $branch_id
 * @property float|null $salary
 * @property string $status
 * @property Carbon $hired_at
 * @property Carbon $created_at
 * 
 * @property Branch $branch
 * @property User|null $user
 * @property Collection|Attendance[] $attendances
 * @property Collection|AuditLog[] $audit_logs
 * @property Collection|BreakSlot[] $break_slots
 * @property Collection|BundleUsageLog[] $bundle_usage_logs
 * @property Collection|ContractType[] $contract_types
 * @property Collection|CourseInstance[] $course_instances
 * @property Collection|CourseTemplate[] $course_templates
 * @property Collection|CsTarget[] $cs_targets
 * @property Collection|Enrollment[] $enrollments
 * @property Collection|FinancialTransaction[] $financial_transactions
 * @property Collection|InstallmentApprovalLog[] $installment_approval_logs
 * @property Collection|InstanceSchedule[] $instance_schedules
 * @property Collection|Lead[] $leads
 * @property Collection|LeadCallLog[] $lead_call_logs
 * @property Collection|LeadHistory[] $lead_histories
 * @property Collection|Level[] $levels
 * @property Collection|Offer[] $offers
 * @property Collection|Patch[] $patches
 * @property Collection|PaymentPlan[] $payment_plans
 * @property Collection|PlacementTest[] $placement_tests
 * @property Collection|Postponement[] $postponements
 * @property Collection|PrivateBundle[] $private_bundles
 * @property Collection|RefundRequest[] $refund_requests
 * @property Collection|Report[] $reports
 * @property Collection|RestrictionLog[] $restriction_logs
 * @property Collection|RevenueSplit[] $revenue_splits
 * @property Collection|Room[] $rooms
 * @property Collection|ScheduleChangeLog[] $schedule_change_logs
 * @property Collection|Sublevel[] $sublevels
 * @property Teacher|null $teacher
 * @property Collection|TimeSlot[] $time_slots
 * @property Collection|UserNotification[] $user_notifications
 * @property Collection|WaitingList[] $waiting_lists
 *
 * @package App\Models
 */
class Employee extends Model
{
	protected $table = 'employee';
	protected $primaryKey = 'employee_id';
	public $timestamps = false;

	protected $casts = [
		'user_id' => 'int',
		'branch_id' => 'int',
		'salary' => 'float',
		'hired_at' => 'datetime'
	];

	protected $fillable = [
		'full_name',
		'user_id',
		'branch_id',
		'salary',
		'status',
		'hired_at'
	];

	public function branch()
	{
		return $this->belongsTo(Branch::class);
	}

	public function user()
	{
		return $this->belongsTo(User::class);
	}

	public function attendances()
	{
		return $this->hasMany(Attendance::class, 'recorded_by');
	}

	public function audit_logs()
	{
		return $this->hasMany(AuditLog::class, 'changed_by');
	}

	public function break_slots()
	{
		return $this->hasMany(BreakSlot::class, 'created_by_admin_id');
	}

	public function bundle_usage_logs()
	{
		return $this->hasMany(BundleUsageLog::class, 'created_by_cs_id');
	}

	public function contract_types()
	{
		return $this->hasMany(ContractType::class, 'created_by_admin_id');
	}

	public function course_instances()
	{
		return $this->hasMany(CourseInstance::class, 'created_by_employee_id');
	}

	public function course_templates()
	{
		return $this->hasMany(CourseTemplate::class, 'created_by_admin_id');
	}

	public function cs_targets()
	{
		return $this->hasMany(CsTarget::class);
	}

	public function enrollments()
	{
		return $this->hasMany(Enrollment::class, 'created_by_cs_id');
	}

	public function financial_transactions()
	{
		return $this->hasMany(FinancialTransaction::class, 'created_by_employee_id');
	}

	public function installment_approval_logs()
	{
		return $this->hasMany(InstallmentApprovalLog::class, 'request_by_cs_id');
	}

	public function instance_schedules()
	{
		return $this->hasMany(InstanceSchedule::class, 'created_by_employee_id');
	}

	public function leads()
	{
		return $this->hasMany(Lead::class, 'owner_cs_id');
	}

	public function lead_call_logs()
	{
		return $this->hasMany(LeadCallLog::class, 'cs_id');
	}

	public function lead_histories()
	{
		return $this->hasMany(LeadHistory::class, 'changed_by');
	}

	public function levels()
	{
		return $this->hasMany(Level::class, 'created_by_admin_id');
	}

	public function offers()
	{
		return $this->hasMany(Offer::class, 'created_by_admin_id');
	}

	public function patches()
	{
		return $this->hasMany(Patch::class, 'created_by_admin_id');
	}

	public function payment_plans()
	{
		return $this->hasMany(PaymentPlan::class, 'created_by_admin_id');
	}

	public function placement_tests()
	{
		return $this->hasMany(PlacementTest::class, 'created_by_cs_id');
	}

	public function postponements()
	{
		return $this->hasMany(Postponement::class, 'created_by_cs_id');
	}

	public function private_bundles()
	{
		return $this->hasMany(PrivateBundle::class, 'created_by_admin_id');
	}

	public function refund_requests()
	{
		return $this->hasMany(RefundRequest::class, 'requested_by');
	}

	public function reports()
	{
		return $this->hasMany(Report::class, 'approved_by_admin_id');
	}

	public function restriction_logs()
	{
		return $this->hasMany(RestrictionLog::class, 'released_by');
	}

	public function revenue_splits()
	{
		return $this->hasMany(RevenueSplit::class);
	}

	public function rooms()
	{
		return $this->hasMany(Room::class, 'created_by_admin_id');
	}

	public function schedule_change_logs()
	{
		return $this->hasMany(ScheduleChangeLog::class, 'changed_by_employee_id');
	}

	public function sublevels()
	{
		return $this->hasMany(Sublevel::class, 'created_by_admin_id');
	}

	public function teacher()
	{
		return $this->hasOne(Teacher::class);
	}

	public function time_slots()
	{
		return $this->hasMany(TimeSlot::class, 'created_by_admin_id');
	}

	public function user_notifications()
	{
		return $this->hasMany(UserNotification::class);
	}

	public function waiting_lists()
	{
		return $this->hasMany(WaitingList::class, 'created_by_cs_id');
	}
}
