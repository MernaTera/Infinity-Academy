<?php


namespace App\Models\Core;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

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
		'is_active' => 'bool'
	];

	protected $fillable = [
		'name',
		'code',
		'address',
		'phone',
		'is_active'
	];

	public function course_instances()
	{
		return $this->hasMany(CourseInstance::class, 'branch_id');
	}

	public function employees()
	{
		return $this->hasMany(Employee::class, 'branch_id');
	}

	public function financial_transactions()
	{
		return $this->hasMany(FinancialTransaction::class, 'branch_id');
	}

	public function patches()
	{
		return $this->hasMany(Patch::class, 'branch_id');
	}

	public function revenue_splits()
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
