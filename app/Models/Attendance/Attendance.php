<?php



namespace App\Models\Attendance;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use App\Models\HR\Employee;
use App\Models\Enrollment\Enrollment;
use App\Models\Academic\CourseSession;

/**
 * Class Attendance
 * 
 * @property int $attendance_id
 * @property int $enrollment_id
 * @property int $course_session_id
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
		'enrollment_id' => 'integer',
		'course_session_id' => 'integer',
		'recorded_by' => 'integer',
		'recorded_at' => 'datetime'
	];

	protected $fillable = [
		'enrollment_id',
		'course_session_id',
		'status',
		'recorded_by',
		'recorded_at'
	];

	public function recordedBy()
	{
		return $this->belongsTo(Employee::class, 'recorded_by');
	}

	public function enrollment()
	{
		return $this->belongsTo(Enrollment::class, 'enrollment_id');
	}

	public function session()
	{
		return $this->belongsTo(Session::class, 'course_session_id');
	}
	
	public function isPresent()
	{
		return $this->status === 'Present';
	}

	public function isAbsent()
	{
		return $this->status === 'Absent';
	}
}
