<?php


namespace App\Models\Finance;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use App\Models\HR\Employee;
use App\Models\Finance\InstallmentApprovalLog;
use App\Models\Enrollment\Enrollment;

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
		'deposit_percentage' => 'decimal:2',
		'installment_count' => 'integer',
		'requires_admin_approval' => 'boolean',
		'grace_period_days' => 'integer',
		'is_active' => 'bool',
		'created_by_admin_id' => 'integer'
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

	public function createdBy()
	{
		return $this->belongsTo(Employee::class, 'created_by_admin_id');
	}

	public function installmentApprovalLogs()
	{
		return $this->hasMany(InstallmentApprovalLog::class, 'payment_plan_id');
	}

	public function enrollments()
	{
		return $this->hasMany(Enrollment::class, 'payment_plan_id');
	}

    public function isActive()
    {
        return $this->is_active === true;
    }

    public function requiresApproval()
    {
        return $this->requires_admin_approval === true;
    }

    public function hasInstallments()
    {
        return $this->installment_count > 0;
    }

    public function hasDeposit()
    {
        return $this->deposit_percentage > 0;
    }

    public function calculateDeposit($price)
    {
        if (!$this->hasDeposit()) {
            return 0;
        }

        return ($price * $this->deposit_percentage) / 100;
    }

    public function calculateRemainingAmount($price)
    {
        return $price - $this->calculateDeposit($price);
    }

    public function calculateInstallmentAmount($price)
    {
        if (!$this->hasInstallments()) {
            return 0;
        }

        $remaining = $this->calculateRemainingAmount($price);

        return $remaining / $this->installment_count;
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
