<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Teacher
 * 
 * @property int $teacher_id
 * @property int $employee_id
 * @property int $english_level_id
 * @property bool $is_active
 * @property Carbon $created_at
 * 
 * @property Employee $employee
 * @property EnglishLevel $english_level
 * @property Collection|ContractType[] $contract_types
 * @property Collection|CourseInstance[] $course_instances
 * @property Collection|Report[] $reports
 * @property Collection|TeacherAvailability[] $teacher_availabilities
 *
 * @package App\Models
 */
class Teacher extends Model
{
	protected $table = 'teacher';
	protected $primaryKey = 'teacher_id';
	public $timestamps = false;

	protected $casts = [
		'employee_id' => 'int',
		'english_level_id' => 'int',
		'is_active' => 'bool'
	];

	protected $fillable = [
		'employee_id',
		'english_level_id',
		'is_active'
	];

	public function employee()
	{
		return $this->belongsTo(Employee::class);
	}

	public function english_level()
	{
		return $this->belongsTo(EnglishLevel::class);
	}

	public function contract_types()
	{
		return $this->hasMany(ContractType::class);
	}

	public function course_instances()
	{
		return $this->hasMany(CourseInstance::class);
	}

	public function reports()
	{
		return $this->hasMany(Report::class);
	}

	public function teacher_availabilities()
	{
		return $this->hasMany(TeacherAvailability::class);
	}
}
