<?php


namespace App\Models\Finance;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use App\Models\Core\Branch;
use App\Models\HR\Employee;
use App\Models\Academic\Patch;
use App\Models\Finance\FinancialTransaction;

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
	public $timestamps = true;

	protected $casts = [
		'original_split_id' => 'integer',
		'transaction_id' => 'integer',
		'employee_id' => 'integer',
		'branch_id' => 'integer',
		'patch_id' => 'integer',
		'amount_allocated' => 'decimal:2',
		'created_at' => 'datetime',
		'updated_at' => 'datetime'
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

	public function revenueSplit()
	{
		return $this->belongsTo(RevenueSplit::class, 'original_split_id');
	}

	public function branch()
	{
		return $this->belongsTo(Branch::class, 'branch_id');
	}

	public function employee()
	{
		return $this->belongsTo(Employee::class, 'employee_id');
	}

	public function patch()
	{
		return $this->belongsTo(Patch::class, 'patch_id');
	}

	public function financialTransaction()
	{
		return $this->belongsTo(FinancialTransaction::class, 'transaction_id');
	}

	public function parentSplit()
	{
		return $this->belongsTo(RevenueSplit::class, 'original_split_id');
	}

	public function childSplits()
	{
		return $this->hasMany(RevenueSplit::class, 'original_split_id');
	}

    public function isDirect()
    {
        return $this->allocation_type === 'Direct';
    }

    public function isShared()
    {
        return $this->allocation_type === 'Shared';
    }

    public function isBonus()
    {
        return $this->allocation_type === 'Bonus';
    }

    public function isRootSplit()
    {
        return $this->original_split_id === $this->revenue_split_id
            || $this->original_split_id === null;
    }

    public function totalChildrenAmount()
    {
        return $this->childSplits()->sum('amount_allocated');
    }

    public function scopeDirect($query)
    {
        return $query->where('allocation_type', 'Direct');
    }

    public function scopeShared($query)
    {
        return $query->where('allocation_type', 'Shared');
    }

    public function scopeBonus($query)
    {
        return $query->where('allocation_type', 'Bonus');
    }
}
