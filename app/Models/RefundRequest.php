<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

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
	public $timestamps = false;

	protected $casts = [
		'enrollment_id' => 'int',
		'requested_by' => 'int',
		'amount' => 'float',
		'approved_by_admin_id' => 'int',
		'approved_at' => 'datetime',
		'processed_transaction_id' => 'int'
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

	public function employee()
	{
		return $this->belongsTo(Employee::class, 'requested_by');
	}

	public function enrollment()
	{
		return $this->belongsTo(Enrollment::class);
	}

	public function financial_transaction()
	{
		return $this->belongsTo(FinancialTransaction::class, 'processed_transaction_id');
	}
}
