<?php


namespace App\Models\Academic;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use App\Models\HR\Employee;
use App\Models\Academic\CourseInstance;

/**
 * Class ScheduleChangeLog
 * 
 * @property int $change_id
 * @property int $course_instance_id
 * @property string $old_schedule
 * @property string $new_schedule
 * @property Carbon $effective_from
 * @property int $changed_by_employee_id
 * @property Carbon|null $changed_at
 * 
 * @property Employee $employee
 * @property CourseInstance $course_instance
 *
 * @package App\Models
 */
class ScheduleChangeLog extends Model
{
	protected $table = 'schedule_change_log';
	protected $primaryKey = 'change_id';
	public $timestamps = false;

	protected $casts = [
		'course_instance_id' => 'integer',
		'old_schedule' => 'array',
		'new_schedule' => 'array',
		'effective_from' => 'date',
		'changed_by_employee_id' => 'integer',
		'changed_at' => 'datetime'
	];

	protected $fillable = [
		'course_instance_id',
		'old_schedule',
		'new_schedule',
		'effective_from',
		'changed_by_employee_id',
		'changed_at'
	];

	public function changedBy()
	{
		return $this->belongsTo(Employee::class, 'changed_by_employee_id');
	}

	public function courseInstance()
	{
		return $this->belongsTo(CourseInstance::class, 'course_instance_id');
	}

	public function hasScheduledChanged()
	{
		return $this->old_schedule !== $this->new_schedule;
	}
}
