<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Level
 * 
 * @property int $level_id
 * @property int $course_template_id
 * @property string $name
 * @property float $price
 * @property int $level_order
 * @property float $total_hours
 * @property float $default_session_duration
 * @property int $max_capacity
 * @property int $teacher_level
 * @property bool|null $is_active
 * @property int|null $created_by_admin_id
 * @property Carbon|null $created_at
 * 
 * @property Employee|null $employee
 * @property CourseTemplate $course_template
 * @property EnglishLevel $english_level
 * @property Collection|CourseInstance[] $course_instances
 * @property Collection|Enrollment[] $enrollments
 * @property Collection|Lead[] $leads
 * @property Collection|PlacementTest[] $placement_tests
 * @property Collection|Sublevel[] $sublevels
 *
 * @package App\Models
 */
class Level extends Model
{
	protected $table = 'level';
	protected $primaryKey = 'level_id';
	public $timestamps = false;

	protected $casts = [
		'course_template_id' => 'int',
		'price' => 'float',
		'level_order' => 'int',
		'total_hours' => 'float',
		'default_session_duration' => 'float',
		'max_capacity' => 'int',
		'teacher_level' => 'int',
		'is_active' => 'bool',
		'created_by_admin_id' => 'int'
	];

	protected $fillable = [
		'course_template_id',
		'name',
		'price',
		'level_order',
		'total_hours',
		'default_session_duration',
		'max_capacity',
		'teacher_level',
		'is_active',
		'created_by_admin_id'
	];

	public function employee()
	{
		return $this->belongsTo(Employee::class, 'created_by_admin_id');
	}

	public function course_template()
	{
		return $this->belongsTo(CourseTemplate::class);
	}

	public function english_level()
	{
		return $this->belongsTo(EnglishLevel::class, 'teacher_level');
	}

	public function course_instances()
	{
		return $this->hasMany(CourseInstance::class);
	}

	public function enrollments()
	{
		return $this->hasMany(Enrollment::class);
	}

	public function leads()
	{
		return $this->hasMany(Lead::class, 'interested_level_id');
	}

	public function placement_tests()
	{
		return $this->hasMany(PlacementTest::class, 'override_level_id');
	}

	public function sublevels()
	{
		return $this->hasMany(Sublevel::class);
	}
}
