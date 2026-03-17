<?php

namespace App\Models\HR;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

use App\Models\HR\Teacher;
use App\Models\Academic\TimeSlot;

/**
 * Class TeacherAvailability
 * 
 * @property int $availability_id
 * @property int $teacher_id
 * @property int $time_slot_id
 * @property string|null $day_of_week
 * @property Carbon|null $created_at
 * 
 * @property TimeSlot $time_slot
 * @property Teacher $teacher
 *
 * @package App\Models
 */

class TeacherAvailability extends Model
{
	protected $table = 'teacher_availability';
	protected $primaryKey = 'availability_id';
	public $timestamps = true;

	protected $casts = [
		'teacher_id' => 'integer',
		'time_slot_id' => 'integer',
		'created_at' => 'datetime'
	];

	protected $fillable = [
		'teacher_id',
		'time_slot_id',
		'day_of_week'
	];

	public function timeSlot()
	{
		return $this->belongsTo(TimeSlot::class, 'time_slot_id');
	}

	public function teacher()
	{
		return $this->belongsTo(Teacher::class, 'teacher_id');
	}

	public function getDayNameAttribute()
	{
		return $this->day_of_week;
	}

	public function scopeForDay($query, $day)
	{
		return $query->where('day_of_week', $day);
	}
}
