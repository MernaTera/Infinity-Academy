<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

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
	public $timestamps = false;

	protected $casts = [
		'enrollment_id' => 'int',
		'transaction_id' => 'int',
		'installment_number' => 'int',
		'due_date' => 'datetime',
		'due_session_number' => 'int',
		'amount' => 'float',
		'paid_at' => 'datetime'
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
		return $this->belongsTo(Enrollment::class);
	}

	public function financial_transaction()
	{
		return $this->belongsTo(FinancialTransaction::class, 'transaction_id');
	}
}
