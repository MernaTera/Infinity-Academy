<?php


namespace App\Models\Academic;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use App\Models\Core\Branch;
use App\Models\Academic\CourseTemplate;
use App\Models\HR\Employee;
use App\Models\Academic\Level;
use App\Models\Academic\Patch;
use App\Models\Academic\Room;
use App\Models\Academic\Sublevel;
use App\Models\HR\Teacher;
use App\Models\Enrollment\Enrollment;
use App\Models\Academic\InstanceSchedule;
use App\Models\Academic\ScheduleChangeLog;
use App\Models\Academic\CourseSession;

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
	public $timestamps = true;

	protected $casts = [
		'course_template_id' => 'integer',
		'level_id' => 'integer',
		'sublevel_id' => 'integer',
		'patch_id' => 'integer',
		'teacher_id' => 'integer',
		'branch_id' => 'integer',
		'start_date' => 'date',
		'end_date' => 'date',
		'room_id' => 'integer',
		'total_hours' => 'decimal:2',
		'session_duration' => 'decimal:2',
		'capacity' => 'integer',
		'created_by_employee_id' => 'integer',
		'created_at' => 'datetime'
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
		return $this->belongsTo(Branch::class, 'branch_id');
	}

	public function courseTemplate()
	{
		return $this->belongsTo(CourseTemplate::class, 'course_template_id');
	}

	public function createdBy()
	{
		return $this->belongsTo(Employee::class, 'created_by_employee_id');
	}

	public function level()
	{
		return $this->belongsTo(Level::class, 'level_id');
	}

	public function patch()
	{
		return $this->belongsTo(Patch::class, 'patch_id');
	}

	public function room()
	{
		return $this->belongsTo(Room::class, 'room_id');
	}

	public function sublevel()
	{
		return $this->belongsTo(Sublevel::class, 'sublevel_id');
	}

	public function teacher()
	{
		return $this->belongsTo(Teacher::class, 'teacher_id');
	}

	public function enrollments()
	{
		return $this->hasMany(Enrollment::class, 'course_instance_id');
	}

	public function instanceSchedules()
	{
		return $this->hasMany(InstanceSchedule::class, 'course_instance_id');
	}

	public function scheduleChangeLogs()
	{
		return $this->hasMany(ScheduleChangeLog::class, 'course_instance_id');
	}

	public function sessions()
	{
		return $this->hasMany(CourseSession::class, 'course_instance_id');
	}

	public function totalSessions()
	{
		return $this->total_hours / $this->session_duration;
	}

	public function isPrivate()
	{
		return $this->type === 'Private';
	}

	public function isActive()
	{
		return $this->status === 'Active';
	}

	public function isCompleted()
	{
		return $this->status === 'Completed';
	}

	public function isCancelled()
	{
		return $this->status === 'Cancelled';
	}

	public function isFull()
	{
		return $this->enrollments()->count() >= $this->capacity;
	}

	public function hasScheduleConflict($dayOfWeek, $timeSlotId)
	{
		return $this->instanceSchedules()
			->where('day_of_week', $dayOfWeek)
			->where('time_slot_id', $timeSlotId)
			->exists();
	}

	public function scopeActive($query)
	{
		return $query->where('status', 'Active');
	}

}
