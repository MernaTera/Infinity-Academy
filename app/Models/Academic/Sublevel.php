<?php


namespace App\Models\Academic;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use App\Models\HR\Employee;
use App\Models\Academic\Level;
use App\Models\Academic\CourseInstance;
use App\Models\Academic\EnglishLevel;
use App\Models\Enrollment\Enrollment;
use App\Models\Leads\Lead;

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
	public $timestamps = true;

	protected $casts = [
		'level_id' => 'integer',
		'sublevel_order' => 'integer',
		'total_hours' => 'decimal:2',
		'default_session_duration' => 'decimal:2',
		'max_capacity' => 'integer',
		'teacher_min_level' => 'integer',
		'price' => 'decimal:2',
		'is_active' => 'boolean',
		'created_by_admin_id' => 'integer',
		'created_at' => 'datetime'
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

	public function createdByAdmin()
	{
		return $this->belongsTo(Employee::class, 'created_by_admin_id');
	}

	public function level()
	{
		return $this->belongsTo(Level::class, 'level_id');
	}

	public function teacherMinLevel()
	{
		return $this->belongsTo(EnglishLevel::class, 'teacher_min_level');
	}

	public function courseInstances()
	{
		return $this->hasMany(CourseInstance::class, 'sublevel_id');
	}

	public function enrollments()
	{
		return $this->hasMany(Enrollment::class, 'sublevel_id');
	}

	public function leads()
	{
		return $this->hasMany(Lead::class, 'interested_sublevel_id');
	}

	public function totalSessions()
	{
		if ($this->default_session_duration > 0) {
			return ceil($this->total_hours / $this->default_session_duration);
		}
		return null;
	}
	
	public function isActive()
	{
		return $this->is_active === true;
	}

	public function scopeOrdered($query)
	{
		return $query->orderBy('sublevel_order');
	}
}
