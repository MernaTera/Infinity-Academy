<?php


namespace App\Models\Core;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use App\Models\Course\CourseInstance;
use App\Models\HR\Employee;
use App\Models\Financial\FinancialTransaction;
use App\Models\Academic\Patch;
use App\Models\Finance\RevenueSplit;
use App\Models\Academic\Room;

/**
 * Class Branch
 * 
 * @property int $branch_id
 * @property string $name
 * @property string|null $code
 * @property string|null $address
 * @property string|null $phone
 * @property bool|null $is_active
 * @property Carbon|null $created_at
 * 
 * @property Collection|CourseInstance[] $course_instances
 * @property Collection|Employee[] $employees
 * @property Collection|FinancialTransaction[] $financial_transactions
 * @property Collection|Patch[] $patches
 * @property Collection|RevenueSplit[] $revenue_splits
 * @property Collection|Room[] $rooms
 *
 * @package App\Models
 */
class Branch extends Model
{
	protected $table = 'branch';
	protected $primaryKey = 'branch_id';
	public $timestamps = false;

	protected $casts = [
		'is_active' => 'boolean'
	];

	protected $fillable = [
		'name',
		'code',
		'address',
		'phone',
		'is_active'
	];

	public function courseInstances()
	{
		return $this->hasMany(CourseInstance::class, 'branch_id');
	}

	public function employees()
	{
		return $this->hasMany(Employee::class, 'branch_id');
	}

	public function financialTransactions()
	{
		return $this->hasMany(FinancialTransaction::class, 'branch_id');
	}

	public function patches()
	{
		return $this->hasMany(Patch::class, 'branch_id');
	}

	public function revenueSplits()
	{
		return $this->hasMany(RevenueSplit::class, 'branch_id');
	}

	public function rooms()
	{
		return $this->hasMany(Room::class, 'branch_id');
	}

	public function scopeActive($query)
	{
    	return $query->where('is_active', true);
	}

	public function getDisplayNameAttribute()
	{
    	return $this->name . ' (' . $this->code . ')';
	}
}
