<?php


namespace App\Models\Finance;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use App\Models\HR\Employee;
use App\Models\Enrollment\Enrollment;
use App\Models\Core\Branch;
use App\Models\Academic\Patch;
use App\Models\Finance\InstallmentSchedule;
use App\Models\Finance\RefundRequest;
use App\Models\Finance\RevenueSplit;

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
		'enrollment_id' => 'integer',
		'patch_id' => 'integer',
		'branch_id' => 'integer',
		'amount' => 'decimal:2',
		'created_by_employee_id' => 'integer',
		'created_at' => 'datetime'
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
		return $this->belongsTo(Branch::class, 'branch_id');
	}

	public function createdBy()
	{
		return $this->belongsTo(Employee::class, 'created_by_employee_id');
	}

	public function enrollment()
	{
		return $this->belongsTo(Enrollment::class, 'enrollment_id');
	}

	public function patch()
	{
		return $this->belongsTo(Patch::class, 'patch_id');
	}

	public function installmentSchedules()
	{
		return $this->hasMany(InstallmentSchedule::class, 'transaction_id');
	}

	public function refundRequests()
	{
		return $this->hasMany(RefundRequest::class, 'processed_transaction_id');
	}

	public function revenueSplits()
	{
		return $this->hasMany(RevenueSplit::class, 'transaction_id');
	}

    public function isIncome()
    {
        return $this->transaction_type === 'Income';
    }

    public function isExpense()
    {
        return $this->transaction_type === 'Expense';
    }

    public function isRefund()
    {
        return $this->transaction_category === 'Refund';
    }

    public function isCoursePayment()
    {
        return $this->transaction_category === 'Course_Payment';
    }

    public function isInstallment()
    {
        return $this->transaction_category === 'Installment';
    }

    public function isCash()
    {
        return $this->payment_method === 'Cash';
    }

    public function isCard()
    {
        return $this->payment_method === 'Card';
    }

    public function isTransfer()
    {
        return $this->payment_method === 'Bank_Transfer';
    }

}
