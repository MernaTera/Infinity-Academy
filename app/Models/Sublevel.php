<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Sublevel
 * 
 * @property int $sublevel_id
 * @property int $level_id
 * @property string $name
 * @property int $sublevel_order
 * @property float $total_hours
 * @property float|null $default_session_duration
 * @property int $max_capacity
 * @property int|null $teacher_min_level
 * @property float|null $price
 * @property bool|null $is_active
 * @property int|null $created_by_admin_id
 * @property Carbon|null $created_at
 * 
 * @property Employee|null $employee
 * @property Level $level
 * @property EnglishLevel|null $english_level
 * @property Collection|CourseInstance[] $course_instances
 * @property Collection|Enrollment[] $enrollments
 * @property Collection|Lead[] $leads
 *
 * @package App\Models
 */
class Sublevel extends Model
{
	protected $table = 'sublevel';
	protected $primaryKey = 'sublevel_id';
	public $timestamps = false;

	protected $casts = [
		'level_id' => 'int',
		'sublevel_order' => 'int',
		'total_hours' => 'float',
		'default_session_duration' => 'float',
		'max_capacity' => 'int',
		'teacher_min_level' => 'int',
		'price' => 'float',
		'is_active' => 'bool',
		'created_by_admin_id' => 'int'
	];

	protected $fillable = [
		'level_id',
		'name',
		'sublevel_order',
		'total_hours',
		'default_session_duration',
		'max_capacity',
		'teacher_min_level',
		'price',
		'is_active',
		'created_by_admin_id'
	];

	public function employee()
	{
		return $this->belongsTo(Employee::class, 'created_by_admin_id');
	}

	public function level()
	{
		return $this->belongsTo(Level::class);
	}

	public function english_level()
	{
		return $this->belongsTo(EnglishLevel::class, 'teacher_min_level');
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
		return $this->hasMany(Lead::class, 'interested_sublevel_id');
	}
}
