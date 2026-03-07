<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

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
	public $timestamps = false;

	protected $casts = [
		'private_allowed' => 'bool',
		'private_only' => 'bool',
		'is_active' => 'bool',
		'created_by_admin_id' => 'int'
	];

	protected $fillable = [
		'name',
		'private_allowed',
		'private_only',
		'is_active',
		'created_by_admin_id'
	];

	public function employee()
	{
		return $this->belongsTo(Employee::class, 'created_by_admin_id');
	}

	public function course_instances()
	{
		return $this->hasMany(CourseInstance::class);
	}

	public function leads()
	{
		return $this->hasMany(Lead::class, 'interested_course_template_id');
	}

	public function levels()
	{
		return $this->hasMany(Level::class);
	}

	public function offers()
	{
		return $this->belongsToMany(Offer::class, 'offer_course_template');
	}
}
