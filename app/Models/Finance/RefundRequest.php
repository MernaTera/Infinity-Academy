<?php


namespace App\Models\Finance;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use App\Models\HR\Employee;
use App\Models\Enrollment\Enrollment;
use App\Models\Finance\FinancialTransaction;

/**
 * Class RefundRequest
 * 
 * @property int $request_id
 * @property int $enrollment_id
 * @property int $requested_by
 * @property float $amount
 * @property string|null $reason
 * @property string $status
 * @property int|null $approved_by_admin_id
 * @property Carbon|null $approved_at
 * @property string|null $rejection_note
 * @property int|null $processed_transaction_id
 * @property Carbon|null $created_at
 * 
 * @property Employee $employee
 * @property Enrollment $enrollment
 * @property FinancialTransaction|null $financial_transaction
 *
 * @package App\Models
 */
class RefundRequest extends Model
{
	protected $table = 'refund_request';
	protected $primaryKey = 'request_id';
	public $timestamps = true;

	protected $casts = [
		'enrollment_id' => 'integer',
		'requested_by' => 'integer',
		'amount' => 'decimal:2',
		'approved_by_admin_id' => 'integer',
		'approved_at' => 'datetime',
		'processed_transaction_id' => 'integer',
		'created_at' => 'datetime',
        'updated_at' => 'datetime'
	];

	protected $fillable = [
		'enrollment_id',
		'requested_by',
		'amount',
		'reason',
		'status',
		'approved_by_admin_id',
		'approved_at',
		'rejection_note',
		'processed_transaction_id'
	];

	public function requestedBy()
	{
		return $this->belongsTo(Employee::class, 'requested_by');
	}

	public function enrollment()
	{
		return $this->belongsTo(Enrollment::class, 'enrollment_id');
	}

	public function financialTransaction()
	{
		return $this->belongsTo(FinancialTransaction::class, 'processed_transaction_id');
	}

	public function approvedBy()
	{
		return $this->belongsTo(Employee::class, 'approved_by_admin_id');
	}

    public function isPending()
    {
        return $this->status === 'Pending';
    }

    public function isApproved()
    {
        return $this->status === 'Approved';
    }

    public function isRejected()
    {
        return $this->status === 'Rejected';
    }

    public function isProcessed()
    {
        return $this->status === 'Processed';
    }

    public function approve($adminId)
    {
        $this->update([
            'status' => 'Approved',
            'approved_by_admin_id' => $adminId,
            'approved_at' => now()
        ]);
    }

    public function reject($adminId, $note = null)
    {
        $this->update([
            'status' => 'Rejected',
            'approved_by_admin_id' => $adminId,
            'approved_at' => now(),
            'rejection_note' => $note
        ]);
    }

    public function markAsProcessed($transactionId)
    {
        $this->update([
            'status' => 'Processed',
            'processed_transaction_id' => $transactionId
        ]);
    }

    public function scopePending($query)
    {
        return $query->where('status', 'Pending');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'Approved');
    }

    public function scopeRejected($query)
    {
        return $query->where('status', 'Rejected');
    }

    public function scopeProcessed($query)
    {
        return $query->where('status', 'Processed');
    }
}
