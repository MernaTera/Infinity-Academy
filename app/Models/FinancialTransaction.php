<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class FinancialTransaction
 * 
 * @property int $transaction_id
 * @property int $enrollment_id
 * @property int $patch_id
 * @property int $branch_id
 * @property string $transaction_type
 * @property string $transaction_category
 * @property float $amount
 * @property string $payment_method
 * @property string|null $reference_number
 * @property string|null $notes
 * @property int|null $created_by_employee_id
 * @property Carbon|null $created_at
 * 
 * @property Branch $branch
 * @property Employee|null $employee
 * @property Enrollment $enrollment
 * @property Patch $patch
 * @property Collection|InstallmentSchedule[] $installment_schedules
 * @property Collection|RefundRequest[] $refund_requests
 * @property Collection|RevenueSplit[] $revenue_splits
 *
 * @package App\Models
 */
class FinancialTransaction extends Model
{
	protected $table = 'financial_transaction';
	protected $primaryKey = 'transaction_id';
	public $timestamps = false;

	protected $casts = [
		'enrollment_id' => 'int',
		'patch_id' => 'int',
		'branch_id' => 'int',
		'amount' => 'float',
		'created_by_employee_id' => 'int'
	];

	protected $fillable = [
		'enrollment_id',
		'patch_id',
		'branch_id',
		'transaction_type',
		'transaction_category',
		'amount',
		'payment_method',
		'reference_number',
		'notes',
		'created_by_employee_id'
	];

	public function branch()
	{
		return $this->belongsTo(Branch::class);
	}

	public function employee()
	{
		return $this->belongsTo(Employee::class, 'created_by_employee_id');
	}

	public function enrollment()
	{
		return $this->belongsTo(Enrollment::class);
	}

	public function patch()
	{
		return $this->belongsTo(Patch::class);
	}

	public function installment_schedules()
	{
		return $this->hasMany(InstallmentSchedule::class, 'transaction_id');
	}

	public function refund_requests()
	{
		return $this->hasMany(RefundRequest::class, 'processed_transaction_id');
	}

	public function revenue_splits()
	{
		return $this->hasMany(RevenueSplit::class, 'transaction_id');
	}
}
