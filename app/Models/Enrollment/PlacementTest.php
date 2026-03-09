<?php


namespace App\Models\Enrollment;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use App\Models\HR\Employee;
use App\Models\Academic\Level;
use App\Models\Student\Student;
use App\Models\Enrollment\Enrollment;
use App\Models\Enrollment\FinancialTransaction;

/**
 * Class PlacementTest
 * 
 * @property int $test_id
 * @property int $student_id
 * @property float $score
 * @property int|null $assigned_level_id
 * @property int|null $override_level_id
 * @property float $test_fee
 * @property bool|null $fee_paid
 * @property bool|null $deducted_from_course
 * @property int|null $created_by_cs_id
 * @property Carbon|null $created_at
 * 
 * @property Employee|null $employee
 * @property Level|null $level
 * @property Student $student
 * @property Collection|Enrollment[] $enrollments
 *
 * @package App\Models
 */
class PlacementTest extends Model
{
	protected $table = 'placement_test';
	protected $primaryKey = 'test_id';
	public $timestamps = false;

	protected $casts = [
		'student_id' => 'integer',
		'score' => 'decimal:2',
		'assigned_level_id' => 'integer',
		'override_level_id' => 'integer',
		'test_fee' => 'decimal:2',
		'fee_paid' => 'boolean',
		'deducted_from_course' => 'boolean',
		'created_by_cs_id' => 'integer',
		'created_at' => 'datetime'
	];

	protected $fillable = [
		'student_id',
		'score',
		'assigned_level_id',
		'override_level_id',
		'test_fee',
		'fee_paid',
		'deducted_from_course',
		'created_by_cs_id'
	];

	public function createdBy()
	{
		return $this->belongsTo(Employee::class, 'created_by_cs_id');
	}

	public function overridedLevel()
	{
		return $this->belongsTo(Level::class, 'override_level_id');
	}

	public function student()
	{
		return $this->belongsTo(Student::class, 'student_id');
	}

	public function enrollments()
	{
		return $this->hasMany(Enrollment::class, 'placement_test_id');
	}

	public function assignedLevel()
	{
		return $this->belongsTo(Level::class, 'assigned_level_id');
	}

	public function enrollmentsWithCourseInstances()
	{
		return $this->hasMany(Enrollment::class, 'placement_test_id')->whereNotNull('course_instance_id');
	}

	public function finallevel()
	{
		return $this->overridedLevel ?? $this->assignedLevel;
	}

	public function isPaid()
	{
		return $this->fee_paid;
	}

	public function isDeducted()
	{
		return $this->deducted_from_course === true;
	}
}
