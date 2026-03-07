<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class RevenueSplit
 * 
 * @property int $revenue_split_id
 * @property int $original_split_id
 * @property int $transaction_id
 * @property int $employee_id
 * @property int $branch_id
 * @property int $patch_id
 * @property float $amount_allocated
 * @property string $allocation_type
 * @property Carbon|null $created_at
 * 
 * @property RevenueSplit $revenue_split
 * @property Branch $branch
 * @property Employee $employee
 * @property Patch $patch
 * @property FinancialTransaction $financial_transaction
 * @property Collection|RevenueSplit[] $revenue_splits
 *
 * @package App\Models
 */
class RevenueSplit extends Model
{
	protected $table = 'revenue_split';
	protected $primaryKey = 'revenue_split_id';
	public $timestamps = false;

	protected $casts = [
		'original_split_id' => 'int',
		'transaction_id' => 'int',
		'employee_id' => 'int',
		'branch_id' => 'int',
		'patch_id' => 'int',
		'amount_allocated' => 'float'
	];

	protected $fillable = [
		'original_split_id',
		'transaction_id',
		'employee_id',
		'branch_id',
		'patch_id',
		'amount_allocated',
		'allocation_type'
	];

	public function revenue_split()
	{
		return $this->belongsTo(RevenueSplit::class, 'original_split_id');
	}

	public function branch()
	{
		return $this->belongsTo(Branch::class);
	}

	public function employee()
	{
		return $this->belongsTo(Employee::class);
	}

	public function patch()
	{
		return $this->belongsTo(Patch::class);
	}

	public function financial_transaction()
	{
		return $this->belongsTo(FinancialTransaction::class, 'transaction_id');
	}

	public function revenue_splits()
	{
		return $this->hasMany(RevenueSplit::class, 'original_split_id');
	}
}
