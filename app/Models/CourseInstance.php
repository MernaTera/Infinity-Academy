<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class CourseInstance
 * 
 * @property int $course_instance_id
 * @property int $course_template_id
 * @property int|null $level_id
 * @property int|null $sublevel_id
 * @property int $patch_id
 * @property int $teacher_id
 * @property int $branch_id
 * @property Carbon $start_date
 * @property Carbon $end_date
 * @property int|null $room_id
 * @property string $delivery_mood
 * @property float $total_hours
 * @property float $session_duration
 * @property int $capacity
 * @property string|null $type
 * @property string $status
 * @property int|null $created_by_employee_id
 * @property Carbon|null $created_at
 * 
 * @property Branch $branch
 * @property CourseTemplate $course_template
 * @property Employee|null $employee
 * @property Level|null $level
 * @property Patch $patch
 * @property Room|null $room
 * @property Sublevel|null $sublevel
 * @property Teacher $teacher
 * @property Collection|Enrollment[] $enrollments
 * @property Collection|InstanceSchedule[] $instance_schedules
 * @property Collection|ScheduleChangeLog[] $schedule_change_logs
 * @property Collection|Session[] $sessions
 *
 * @package App\Models
 */
class CourseInstance extends Model
{
	protected $table = 'course_instance';
	protected $primaryKey = 'course_instance_id';
	public $timestamps = false;

	protected $casts = [
		'course_template_id' => 'int',
		'level_id' => 'int',
		'sublevel_id' => 'int',
		'patch_id' => 'int',
		'teacher_id' => 'int',
		'branch_id' => 'int',
		'start_date' => 'datetime',
		'end_date' => 'datetime',
		'room_id' => 'int',
		'total_hours' => 'float',
		'session_duration' => 'float',
		'capacity' => 'int',
		'created_by_employee_id' => 'int'
	];

	protected $fillable = [
		'course_template_id',
		'level_id',
		'sublevel_id',
		'patch_id',
		'teacher_id',
		'branch_id',
		'start_date',
		'end_date',
		'room_id',
		'delivery_mood',
		'total_hours',
		'session_duration',
		'capacity',
		'type',
		'status',
		'created_by_employee_id'
	];

	public function branch()
	{
		return $this->belongsTo(Branch::class);
	}

	public function course_template()
	{
		return $this->belongsTo(CourseTemplate::class);
	}

	public function employee()
	{
		return $this->belongsTo(Employee::class, 'created_by_employee_id');
	}

	public function level()
	{
		return $this->belongsTo(Level::class);
	}

	public function patch()
	{
		return $this->belongsTo(Patch::class);
	}

	public function room()
	{
		return $this->belongsTo(Room::class);
	}

	public function sublevel()
	{
		return $this->belongsTo(Sublevel::class);
	}

	public function teacher()
	{
		return $this->belongsTo(Teacher::class);
	}

	public function enrollments()
	{
		return $this->hasMany(Enrollment::class);
	}

	public function instance_schedules()
	{
		return $this->hasMany(InstanceSchedule::class);
	}

	public function schedule_change_logs()
	{
		return $this->hasMany(ScheduleChangeLog::class);
	}

	public function sessions()
	{
		return $this->hasMany(Session::class);
	}
}
