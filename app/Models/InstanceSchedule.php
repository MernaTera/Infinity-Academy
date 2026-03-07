<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class InstanceSchedule
 * 
 * @property int $instance_schedule_id
 * @property int $course_instance_id
 * @property string|null $day_of_week
 * @property int|null $time_slot_id
 * @property int|null $created_by_employee_id
 * @property Carbon|null $created_at
 * 
 * @property Employee|null $employee
 * @property CourseInstance $course_instance
 * @property TimeSlot|null $time_slot
 *
 * @package App\Models
 */
class InstanceSchedule extends Model
{
	protected $table = 'instance_schedule';
	protected $primaryKey = 'instance_schedule_id';
	public $timestamps = false;

	protected $casts = [
		'course_instance_id' => 'int',
		'time_slot_id' => 'int',
		'created_by_employee_id' => 'int'
	];

	protected $fillable = [
		'course_instance_id',
		'day_of_week',
		'time_slot_id',
		'created_by_employee_id'
	];

	public function employee()
	{
		return $this->belongsTo(Employee::class, 'created_by_employee_id');
	}

	public function course_instance()
	{
		return $this->belongsTo(CourseInstance::class);
	}

	public function time_slot()
	{
		return $this->belongsTo(TimeSlot::class);
	}
}
