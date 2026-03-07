<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Lead
 * 
 * @property int $lead_id
 * @property string|null $full_name
 * @property string|null $phone
 * @property Carbon|null $birthdate
 * @property string|null $location
 * @property string $source
 * @property string $degree
 * @property int|null $interested_course_template_id
 * @property int|null $interested_level_id
 * @property int|null $interested_sublevel_id
 * @property string $status
 * @property string|null $start_preference_type
 * @property Carbon|null $next_call_at
 * @property int|null $owner_cs_id
 * @property bool $is_active
 * @property string|null $notes
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * 
 * @property CourseTemplate|null $course_template
 * @property Level|null $level
 * @property Employee|null $employee
 * @property Sublevel|null $sublevel
 * @property Collection|LeadCallLog[] $lead_call_logs
 * @property Collection|LeadHistory[] $lead_histories
 *
 * @package App\Models
 */
class Lead extends Model
{
	protected $table = 'lead';
	protected $primaryKey = 'lead_id';

	protected $casts = [
		'birthdate' => 'datetime',
		'interested_course_template_id' => 'int',
		'interested_level_id' => 'int',
		'interested_sublevel_id' => 'int',
		'next_call_at' => 'datetime',
		'owner_cs_id' => 'int',
		'is_active' => 'bool'
	];

	protected $fillable = [
		'full_name',
		'phone',
		'birthdate',
		'location',
		'source',
		'degree',
		'interested_course_template_id',
		'interested_level_id',
		'interested_sublevel_id',
		'status',
		'start_preference_type',
		'next_call_at',
		'owner_cs_id',
		'is_active',
		'notes'
	];

	public function course_template()
	{
		return $this->belongsTo(CourseTemplate::class, 'interested_course_template_id');
	}

	public function level()
	{
		return $this->belongsTo(Level::class, 'interested_level_id');
	}

	public function employee()
	{
		return $this->belongsTo(Employee::class, 'owner_cs_id');
	}

	public function sublevel()
	{
		return $this->belongsTo(Sublevel::class, 'interested_sublevel_id');
	}

	public function lead_call_logs()
	{
		return $this->hasMany(LeadCallLog::class);
	}

	public function lead_histories()
	{
		return $this->hasMany(LeadHistory::class);
	}
}
