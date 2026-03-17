<?php


namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use App\Models\Enrollment\Enrollment;
use App\Models\Finance\FinancialTransaction;
use App\Models\Finance\PaymentPlan;


/**
 * Class InstallmentSchedule
 * 
 * @property int $installment_id
 * @property int $enrollment_id
 * @property int $transaction_id
 * @property int $installment_number
 * @property Carbon|null $due_date
 * @property int|null $due_session_number
 * @property float $amount
 * @property string|null $status
 * @property Carbon|null $paid_at
 * @property Carbon|null $created_at
 * 
 * @property Enrollment $enrollment
 * @property FinancialTransaction $financial_transaction
 *
 * @package App\Models
 */
class InstallmentSchedule extends Model
{
	protected $table = 'installment_schedule';
	protected $primaryKey = 'installment_id';
	public $timestamps = true;

	protected $casts = [
		'enrollment_id' => 'integer',
		'transaction_id' => 'integer',
		'installment_number' => 'integer',
		'due_date' => 'date',
		'due_session_number' => 'integer',
		'amount' => 'decimal:2',
		'paid_at' => 'datetime',
		'created_at' => 'datetime',
        'updated_at' => 'datetime'
	];

	protected $fillable = [
		'enrollment_id',
		'transaction_id',
		'installment_number',
		'due_date',
		'due_session_number',
		'amount',
		'status',
		'paid_at'
	];

	public function enrollment()
	{
		return $this->belongsTo(Enrollment::class, 'enrollment_id');
	}

	public function financialTransaction()
	{
		return $this->belongsTo(FinancialTransaction::class, 'transaction_id');
	}

    public function isPending()
    {
        return $this->status === 'Pending';
    }

    public function isPaid()
    {
        return $this->status === 'Paid';
    }

    public function isOverdue()
    {
        return $this->status === 'Overdue';
    }

    public function markAsPaid()
    {
        $this->update([
            'status' => 'Paid',
            'paid_at' => now()
        ]);
    }

    public function markAsOverdue()
    {
        $this->update([
            'status' => 'Overdue'
        ]);
    }

    public function isDueByDate()
    {
        return $this->due_date && now()->greaterThan($this->due_date);
    }

    public function isDueBySession($currentSessionNumber)
    {
        if (!$this->due_session_number) {
            return false;
        }

        return $currentSessionNumber >= $this->due_session_number;
    }

    public function scopePending($query)
    {
        return $query->where('status', 'Pending');
    }

    public function scopePaid($query)
    {
        return $query->where('status', 'Paid');
    }

    public function scopeOverdue($query)
    {
        return $query->where('status', 'Overdue');
    }

    public function scopeDueToday($query)
    {
        return $query->whereDate('due_date', now()->toDateString());
    }

    public function scopeOverdueByDate($query)
    {
        return $query
            ->where('status', 'Pending')
            ->whereDate('due_date', '<', now());
    }

}
