<?php


namespace App\Models\Academic;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use App\Models\HR\Employee;
use App\Models\Academic\CourseTemplate;
use App\Models\Academic\EnglishLevel;
use App\Models\Academic\CourseInstance;
use App\Models\Enrollment\Enrollment;
use App\Models\Leads\Lead;
use App\Models\Enrollment\PlacementTest;
use App\Models\Academic\Sublevel;

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
		'course_template_id' => 'integer',
		'price' => 'decimal:2',
		'level_order' => 'integer',
		'total_hours' => 'decimal:2',
		'default_session_duration' => 'decimal:2',
		'max_capacity' => 'integer',
		'teacher_level' => 'integer',
		'is_active' => 'boolean',
		'created_by_admin_id' => 'integer',
		'created_at' => 'datetime'
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

	public function createdByAdmin()
	{
		return $this->belongsTo(Employee::class, 'created_by_admin_id');
	}

	public function courseTemplate()
	{
		return $this->belongsTo(CourseTemplate::class, 'course_template_id');
	}

	public function teacherlevel()
	{
		return $this->belongsTo(EnglishLevel::class, 'teacher_level');
	}

	public function courseInstances()
	{
		return $this->hasMany(CourseInstance::class, 'level_id');
	}

	public function enrollments()
	{
		return $this->hasMany(Enrollment::class, 'level_id');
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
		return $this->hasMany(Sublevel::class, 'level_id');
	}

	public function totalSessions()
	{
		return ceil($this->total_hours / $this->default_session_duration);
	}

	public function scopeOrdered($query)
	{
		return $query->orderBy('level_order');
	}

}
