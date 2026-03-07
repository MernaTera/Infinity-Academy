<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

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
		'start_time' => 'datetime',
		'end_time' => 'datetime',
		'is_active' => 'bool',
		'created_by_admin_id' => 'int'
	];

	protected $fillable = [
		'name',
		'start_time',
		'end_time',
		'slot_type',
		'is_active',
		'created_by_admin_id'
	];

	public function employee()
	{
		return $this->belongsTo(Employee::class, 'created_by_admin_id');
	}

	public function instance_schedules()
	{
		return $this->hasMany(InstanceSchedule::class);
	}

	public function teacher_availabilities()
	{
		return $this->hasMany(TeacherAvailability::class);
	}
}
