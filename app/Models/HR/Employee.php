<?php


namespace App\Models\HR;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use App\Models\Auth\User;
use App\Models\Core\Branch;
use App\Models\Lead\Lead;
use App\Models\Enrollment\CsTarget;
use App\Models\Finance\RevenueSplit;
use App\Models\Audit\AuditLog;
use App\Models\HR\Teacher;

/**
 * Class Employee
 *
 * @property int $employee_id
 * @property string $full_name
 * @property int|null $user_id
 * @property int $branch_id
 * @property string $status
 * @property float|null $salary
 * @property Carbon $hired_at
 *
 * Relationships
 * @property Branch $branch
 * @property User|null $user
 * @property Teacher|null $teacher
 *
 * @package App\Models
 */


class Employee extends Model
{
	protected $table = 'employee';
	protected $primaryKey = 'employee_id';
	public $timestamps = true;

	protected $casts = [
		'user_id' => 'integer',
		'branch_id' => 'integer',
		'salary' => 'decimal:2',
		'hired_at' => 'datetime',	
		'created_at' => 'datetime'
	];

	protected $fillable = [
		'full_name',
		'user_id',
		'branch_id',
		'salary',
		'status',
		'hired_at'
	];

	public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function teacher()
    {
        return $this->hasOne(Teacher::class, 'employee_id');
    }

    public function leads()
    {
        return $this->hasMany(Lead::class, 'owner_cs_id');
    }

    public function csTargets()
    {
        return $this->hasMany(CsTarget::class, 'employee_id');
    }

    public function revenueSplits()
    {
        return $this->hasMany(RevenueSplit::class, 'employee_id');
    }

    public function auditLogs()
    {
        return $this->hasMany(AuditLog::class, 'changed_by');
    }

    public function isTeacher()
    {
        return $this->teacher()->exists();
    }

    public function isActive()
    {
        return $this->status === 'Active';
    }

	public function scopeActive($query)
	{
		return $query->where('status','Active');
	}
}
