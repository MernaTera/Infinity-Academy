<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

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
		'student_id' => 'int',
		'score' => 'float',
		'assigned_level_id' => 'int',
		'override_level_id' => 'int',
		'test_fee' => 'float',
		'fee_paid' => 'bool',
		'deducted_from_course' => 'bool',
		'created_by_cs_id' => 'int'
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

	public function employee()
	{
		return $this->belongsTo(Employee::class, 'created_by_cs_id');
	}

	public function level()
	{
		return $this->belongsTo(Level::class, 'override_level_id');
	}

	public function student()
	{
		return $this->belongsTo(Student::class);
	}

	public function enrollments()
	{
		return $this->hasMany(Enrollment::class);
	}
}
