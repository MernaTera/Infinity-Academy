<?php


namespace App\Models\Enrollment;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use App\Models\HR\Employee;
use App\Models\Enrollment\Enrollment;

/**
 * Class RestrictionLog
 * 
 * @property int $restriction_id
 * @property int $enrollment_id
 * @property string $triggered_by
 * @property string $reason
 * @property Carbon $triggered_at
 * @property Carbon|null $released_at
 * @property int|null $released_by
 * @property string|null $notes
 * 
 * @property Employee|null $employee
 * @property Enrollment $enrollment
 *
 * @package App\Models
 */
class RestrictionLog extends Model
{
	protected $table = 'restriction_log';
	protected $primaryKey = 'restriction_id';
	public $timestamps = false;

	protected $casts = [
		'enrollment_id' => 'integer',
		'triggered_at' => 'datetime',
		'released_at' => 'datetime',
		'released_by' => 'integer'

	];

	protected $fillable = [
		'enrollment_id',
		'triggered_by',
		'reason',
		'triggered_at',
		'released_at',
		'released_by',
		'notes'
	];

	public function releasedBy()
	{
		return $this->belongsTo(Employee::class, 'released_by');
	}

	public function enrollment()
	{
		return $this->belongsTo(Enrollment::class, 'enrollment_id');
	}

	public function isActive()
	{
		return $this->released_at === null;
	}

	public function isReleased()
	{
		return $this->released_at !== null;
	}

	public function triggeredBySystem()
	{
		return $this->triggered_by === 'System';
	}

	public function triggeredByEmployee()
	{
		return $this->triggered_by === 'CS';
	}

	public function triggeredByAdmin()
	{
		return $this->triggered_by === 'Admin';
	}
}
