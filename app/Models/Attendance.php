<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Attendance
 * 
 * @property int $attendance_id
 * @property int $enrollment_id
 * @property int $session_id
 * @property string $status
 * @property int $recorded_by
 * @property Carbon|null $recorded_at
 * 
 * @property Employee $employee
 * @property Enrollment $enrollment
 * @property Session $session
 *
 * @package App\Models
 */
class Attendance extends Model
{
	protected $table = 'attendance';
	protected $primaryKey = 'attendance_id';
	public $timestamps = false;

	protected $casts = [
		'enrollment_id' => 'int',
		'session_id' => 'int',
		'recorded_by' => 'int',
		'recorded_at' => 'datetime'
	];

	protected $fillable = [
		'enrollment_id',
		'session_id',
		'status',
		'recorded_by',
		'recorded_at'
	];

	public function employee()
	{
		return $this->belongsTo(Employee::class, 'recorded_by');
	}

	public function enrollment()
	{
		return $this->belongsTo(Enrollment::class);
	}

	public function session()
	{
		return $this->belongsTo(Session::class);
	}
}
