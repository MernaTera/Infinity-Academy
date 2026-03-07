<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class PaymentPlan
 * 
 * @property int $payment_plan_id
 * @property string $name
 * @property float $deposit_percentage
 * @property int $installment_count
 * @property bool|null $requires_admin_approval
 * @property int|null $grace_period_days
 * @property bool|null $is_active
 * @property int|null $created_by_admin_id
 * @property Carbon|null $created_at
 * 
 * @property Employee|null $employee
 * @property Collection|InstallmentApprovalLog[] $installment_approval_logs
 *
 * @package App\Models
 */
class PaymentPlan extends Model
{
	protected $table = 'payment_plan';
	protected $primaryKey = 'payment_plan_id';
	public $timestamps = false;

	protected $casts = [
		'deposit_percentage' => 'float',
		'installment_count' => 'int',
		'requires_admin_approval' => 'bool',
		'grace_period_days' => 'int',
		'is_active' => 'bool',
		'created_by_admin_id' => 'int'
	];

	protected $fillable = [
		'name',
		'deposit_percentage',
		'installment_count',
		'requires_admin_approval',
		'grace_period_days',
		'is_active',
		'created_by_admin_id'
	];

	public function employee()
	{
		return $this->belongsTo(Employee::class, 'created_by_admin_id');
	}

	public function installment_approval_logs()
	{
		return $this->hasMany(InstallmentApprovalLog::class);
	}
}
