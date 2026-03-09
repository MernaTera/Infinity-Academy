<?php


namespace App\Models\Academic;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use App\Models\HR\Employee;
use App\Models\HR\TeacherAvailability;
use App\Models\InstanceSchedule;

/**
 * Class TimeSlot
 * 
 * @property int $time_slot_id
 * @property string|null $name
 * @property Carbon $start_time
 * @property Carbon $end_time
 * @property string|null $slot_type
 * @property bool|null $is_active
 * @property int|null $created_by_admin_id
 * @property Carbon|null $created_at
 * 
 * @property Employee|null $employee
 * @property Collection|InstanceSchedule[] $instance_schedules
 * @property Collection|TeacherAvailability[] $teacher_availabilities
 *
 * @package App\Models
 */
class TimeSlot extends Model
{
	protected $table = 'time_slot';
	protected $primaryKey = 'time_slot_id';
	public $timestamps = false;

	protected $casts = [
		'start_time' => 'string',
		'end_time' => 'string',
		'is_active' => 'boolean',
		'created_by_admin_id' => 'integer',
		'created_at' => 'datetime'
	];

	protected $fillable = [
		'name',
		'start_time',
		'end_time',
		'slot_type',
		'is_active',
		'created_by_admin_id'
	];

	public function createdByAdmin()
	{
		return $this->belongsTo(Employee::class, 'created_by_admin_id');
	}

	public function instanceSchedules()
	{
		return $this->hasMany(InstanceSchedule::class, 'time_slot_id');
	}

	public function teacherAvailabilities()
	{
		return $this->hasMany(TeacherAvailability::class, 'time_slot_id');
	}

	public function durationMinutes()
	{
		return \Carbon\Carbon::parse($this->start_time)->diffInMinutes(\Carbon\Carbon::parse($this->end_time));
	}

	public function isMorning()
	{
		return $this->slot_type === 'Morning';
	}

	public function isAfternoon()
	{
		return $this->slot_type === 'Midday';
	}

	public function isEvening()
	{
		return $this->slot_type === 'Night';
	}

	public function scopeActive($query)
	{
		return $query->where('is_active', true);
	}
}
