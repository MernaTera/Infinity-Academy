<?php

namespace App\Models\HR;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

use App\Models\HR\Employee;
use App\Models\Academic\EnglishLevel;
use App\Models\HR\TeacherAvailability;
use App\Models\Academic\CourseInstance;
use App\Models\Academic\ContractType;
use App\Models\Reports\Report;

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
	public $timestamps = true;

	protected $casts = [
		'employee_id' => 'integer',
		'english_level_id' => 'integer',
		'is_active' => 'boolean'
	];

	protected $fillable = [
		'employee_id',
		'english_level_id',
		'is_active'
	];

	public function employee()
	{
		return $this->belongsTo(Employee::class, 'employee_id');
	}

	public function englishLevel()
	{
		return $this->belongsTo(EnglishLevel::class, 'english_level_id');
	}

	public function contractTypes()
	{
		return $this->hasMany(ContractType::class, 'teacher_id');
	}

	public function courseInstances()
	{
		return $this->hasMany(CourseInstance::class, 'teacher_id');
	}

	public function reports()
	{
		return $this->hasMany(Report::class, 'teacher_id');
	}

	public function teacherAvailabilities()
	{
		return $this->hasMany(TeacherAvailability::class, 'teacher_id');
	}

	public function isActive()
	{
		return $this->is_active;
	}

	public function getNameAttribute()
	{
		return $this->employee->full_name ?? null;
	}

	public function getLevelAttribute()
	{
		return $this->english_level->level_name ?? null;
	}
}
