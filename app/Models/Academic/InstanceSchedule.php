<?php


namespace App\Models\Academic;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use App\Models\HR\Employee;
use App\Models\Academic\CourseInstance;
use App\Models\Academic\TimeSlot;

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
	public $timestamps = true;

	protected $casts = [
		'course_instance_id' => 'integer',
		'time_slot_id' => 'integer',
		'start_time' => 'datetime:H:i:s',
		'created_by_employee_id' => 'integer',
		'created_at' => 'datetime'
	];

	protected $fillable = [
		'course_instance_id',
		'day_of_week',
		'time_slot_id',
		'start_time',
		'created_by_employee_id'
	];

	public function createdByEmployee()
	{
		return $this->belongsTo(Employee::class, 'created_by_employee_id');
	}

	public function course_instance()
	{
		return $this->belongsTo(CourseInstance::class, 'course_instance_id');
	}

	public function timeSlot()
	{
		return $this->belongsTo(TimeSlot::class, 'time_slot_id');
	}

	public function getScheduleInfo()
	{
		return [
			'day_of_week' => $this->day_of_week,
			'time_slot' => $this->time_slot ? $this->time_slot->name : null,
			'start_time' => $this->start_time ? $this->start_time->format('H:i:s') : null,
		];
	}

	public function isWeekend()
	{
		return in_array($this->day_of_week,['Friday','Saturday']);
	}

	public function scopeForDay($query, $day)
	{
		return $query->where('day_of_week', $day);
	}
	
}
