<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

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
		'course_instance_id' => 'int',
		'effective_from' => 'datetime',
		'changed_by_employee_id' => 'int',
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

	public function employee()
	{
		return $this->belongsTo(Employee::class, 'changed_by_employee_id');
	}

	public function course_instance()
	{
		return $this->belongsTo(CourseInstance::class);
	}
}
