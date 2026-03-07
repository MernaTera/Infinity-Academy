<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

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
		'branch_id' => 'int',
		'start_date' => 'datetime',
		'end_date' => 'datetime',
		'is_locked' => 'bool',
		'is_placeholder' => 'bool',
		'created_by_admin_id' => 'int'
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

	public function employee()
	{
		return $this->belongsTo(Employee::class, 'created_by_admin_id');
	}

	public function branch()
	{
		return $this->belongsTo(Branch::class);
	}

	public function contract_types()
	{
		return $this->hasMany(ContractType::class);
	}

	public function course_instances()
	{
		return $this->hasMany(CourseInstance::class);
	}

	public function cs_targets()
	{
		return $this->hasMany(CsTarget::class);
	}

	public function enrollments()
	{
		return $this->hasMany(Enrollment::class);
	}

	public function financial_transactions()
	{
		return $this->hasMany(FinancialTransaction::class);
	}

	public function revenue_splits()
	{
		return $this->hasMany(RevenueSplit::class);
	}

	public function waiting_lists()
	{
		return $this->hasMany(WaitingList::class, 'requested_patch_id');
	}
}
