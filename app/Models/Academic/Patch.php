<?php


namespace App\Models\Academic;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use App\Models\HR\Employee;
use App\Models\Core\Branch;
use App\Models\Enrollment\CsTarget;
use App\Models\Enrollment\Enrollment;
use App\Models\Financial\FinancialTransaction;
use App\Models\Finance\RevenueSplit;
use App\Models\Enrollment\WaitingList;

/**
 * Class Patch
 * 
 * @property int $patch_id
 * @property string $name
 * @property int $branch_id
 * @property Carbon $start_date
 * @property Carbon $end_date
 * @property string|null $status
 * @property bool|null $is_locked
 * @property bool|null $is_placeholder
 * @property int|null $created_by_admin_id
 * @property Carbon|null $created_at
 * 
 * @property Employee|null $employee
 * @property Branch $branch
 * @property Collection|ContractType[] $contract_types
 * @property Collection|CourseInstance[] $course_instances
 * @property Collection|CsTarget[] $cs_targets
 * @property Collection|Enrollment[] $enrollments
 * @property Collection|FinancialTransaction[] $financial_transactions
 * @property Collection|RevenueSplit[] $revenue_splits
 * @property Collection|WaitingList[] $waiting_lists
 *
 * @package App\Models
 */
class Patch extends Model
{
	protected $table = 'patch';
	protected $primaryKey = 'patch_id';
	public $timestamps = false;

	protected $casts = [
		'branch_id' => 'integer',
		'start_date' => 'date',
		'end_date' => 'date',
		'is_locked' => 'boolean',
		'is_placeholder' => 'boolean',
		'created_by_admin_id' => 'integer',
		'created_at' => 'datetime'
	];

	protected $fillable = [
		'name',
		'branch_id',
		'start_date',
		'end_date',
		'status',
		'is_locked',
		'is_placeholder',
		'created_by_admin_id'
	];

	public function createdBy()
	{
		return $this->belongsTo(Employee::class, 'created_by_admin_id');
	}

	public function branch()
	{
		return $this->belongsTo(Branch::class, 'branch_id');
	}

	public function contractTypes()
	{
		return $this->hasMany(ContractType::class, 'patch_id');
	}

	public function courseInstances()
	{
		return $this->hasMany(CourseInstance::class, 'patch_id');
	}

	public function csTargets()
	{
		return $this->hasMany(CsTarget::class, 'patch_id');
	}

	public function enrollments()
	{
		return $this->hasMany(Enrollment::class, 'patch_id');
	}

	public function financialTransactions()
	{
		return $this->hasMany(FinancialTransaction::class, 'patch_id');
	}

	public function revenueSplits()
	{
		return $this->hasMany(RevenueSplit::class, 'patch_id');
	}

	public function waitingLists()
	{
		return $this->hasMany(WaitingList::class, 'requested_patch_id');
	}

	public function isActive()
	{
		return $this->status === 'Active';
	}

	public function isUpcoming()
	{
		return $this->status === 'Upcoming';
	}

	public function isClosed()
	{
		return $this->status === 'Closed';
	}

	public function scopeActive($query)
	{
		return $query->where('status', 'Active');
	}
}
