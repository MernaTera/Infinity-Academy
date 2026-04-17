<?php



namespace App\Models\Academic;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use App\Models\HR\Employee;
use App\Models\Leads\Lead;
use App\Models\Academic\CourseInstance;
use App\Models\Academic\Level;
use App\Models\Finance\Offer;
use App\Models\Academic\EnglishLevel;

/**
 * Class CourseTemplate
 * 
 * @property int $course_template_id
 * @property string $name
 * @property bool|null $private_allowed
 * @property bool|null $private_only
 * @property bool|null $is_active
 * @property int|null $created_by_admin_id
 * @property Carbon|null $created_at
 * 
 * @property Employee|null $employee
 * @property Collection|CourseInstance[] $course_instances
 * @property Collection|Lead[] $leads
 * @property Collection|Level[] $levels
 * @property Collection|Offer[] $offers
 *
 * @package App\Models
 */
class CourseTemplate extends Model
{
	protected $table = 'course_template';
	protected $primaryKey = 'course_template_id';
	public $timestamps = true;

	protected $casts = [
		'private_allowed' => 'boolean',
		'private_only' => 'boolean',
		'is_active' => 'boolean',
		'created_by_admin_id' => 'integer',
		'created_at' => 'datetime'
	];

	protected $fillable = [
		'name',
		'price',
		'english_level_id',
		'private_allowed',
		'private_only',
		'is_active',
		'created_by_admin_id'
	];

	public function createdBy()
	{
		return $this->belongsTo(Employee::class, 'created_by_admin_id');
	}

	public function courseInstances()
	{
		return $this->hasMany(CourseInstance::class, 'course_template_id');
	}

	public function englishLevel()
	{
		return $this->belongsTo(EnglishLevel::class, 'english_level_id');
	}

	public function leads()
	{
		return $this->hasMany(Lead::class, 'interested_course_template_id');
	}

	public function levels()
	{
		return $this->hasMany(Level::class, 'course_template_id');
	}

	public function offers()
	{
		return $this->belongsToMany(Offer::class, 'offer_course_template', 'course_template_id', 'offer_id');
	}

	public function allowsPrivate()
	{
		return $this->private_allowed === true;
	}

	public function isPrivateOnly()
	{
		return $this->private_only === true;
	}

	public function scopeActive($query)
	{
		return $query->where('is_active', true);
	}

}
