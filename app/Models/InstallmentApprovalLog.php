<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

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
	public $timestamps = false;

	protected $casts = [
		'enrollment_id' => 'int',
		'payment_plan_id' => 'int',
		'request_by_cs_id' => 'int',
		'approved_by_admin_id' => 'int',
		'approved_at' => 'datetime'
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

	public function employee()
	{
		return $this->belongsTo(Employee::class, 'request_by_cs_id');
	}

	public function enrollment()
	{
		return $this->belongsTo(Enrollment::class);
	}

	public function payment_plan()
	{
		return $this->belongsTo(PaymentPlan::class);
	}
}
