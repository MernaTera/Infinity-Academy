<?php


namespace App\Models\Finance;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use App\Models\HR\Employee;
use App\Models\Enrollment\Enrollment;
use App\Models\Finance\PaymentPlan;

/**
 * Class InstallmentApprovalLog
 * 
 * @property int $approval_id
 * @property int $enrollment_id
 * @property int $payment_plan_id
 * @property int $request_by_cs_id
 * @property string $status
 * @property int|null $approved_by_admin_id
 * @property Carbon|null $approved_at
 * @property string|null $rejection_note
 * @property Carbon|null $created_at
 * 
 * @property Employee $employee
 * @property Enrollment $enrollment
 * @property PaymentPlan $payment_plan
 *
 * @package App\Models
 */
class InstallmentApprovalLog extends Model
{
	protected $table = 'installment_approval_log';
	protected $primaryKey = 'approval_id';
	public $timestamps = true;

	protected $casts = [
		'enrollment_id' => 'integer',
		'payment_plan_id' => 'integer',
		'request_by_cs_id' => 'integer',
		'approved_by_admin_id' => 'integer',
		'approved_at' => 'datetime',
		'created_at' => 'datetime',
        'updated_at' => 'datetime'
	];

	protected $fillable = [
		'enrollment_id',
		'payment_plan_id',
		'request_by_cs_id',
		'status',
		'approved_by_admin_id',
		'approved_at',
		'rejection_note'
	];

	public function requestedBy()
	{
		return $this->belongsTo(Employee::class, 'request_by_cs_id');
	}

	public function enrollment()
	{
		return $this->belongsTo(Enrollment::class, 'enrollment_id');
	}

	public function paymentPlan()
	{
		return $this->belongsTo(PaymentPlan::class, 'payment_plan_id');
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

    public function approve($adminId)
    {
        $this->update([
            'status' => 'Approved',
            'approved_by_admin_id' => $adminId,
            'approved_at' => now(),
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
}
