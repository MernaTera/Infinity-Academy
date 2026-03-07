<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

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
	public $timestamps = false;

	protected $casts = [
		'teacher_id' => 'int',
		'time_slot_id' => 'int'
	];

	protected $fillable = [
		'teacher_id',
		'time_slot_id',
		'day_of_week'
	];

	public function time_slot()
	{
		return $this->belongsTo(TimeSlot::class);
	}

	public function teacher()
	{
		return $this->belongsTo(Teacher::class);
	}
}
