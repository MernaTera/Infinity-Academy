<?php


namespace App\Models\Leads;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use App\Models\Academic\CourseTemplate;
use App\Models\Academic\Level;
use App\Models\HR\Employee;
use App\Models\Academic\Sublevel;
use App\Models\Leads\LeadCallLog;
use App\Models\Leads\LeadHistory;

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
		'interested_course_template_id' => 'integer',
		'interested_level_id' => 'integer',
		'interested_sublevel_id' => 'integer',
		'next_call_at' => 'datetime',
		'owner_cs_id' => 'integer',
		'is_active' => 'boolean',
		'created_at' => 'datetime',
		'updated_at' => 'datetime'
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

	public function courseTemplate()
	{
		return $this->belongsTo(CourseTemplate::class, 'interested_course_template_id');
	}

	public function level()
	{
		return $this->belongsTo(Level::class, 'interested_level_id');
	}

	public function owner()
	{
		return $this->belongsTo(Employee::class, 'owner_cs_id');
	}

	public function sublevel()
	{
		return $this->belongsTo(Sublevel::class, 'interested_sublevel_id');
	}

	public function leadCallLogs()
	{
		return $this->hasMany(LeadCallLog::class);
	}

	public function leadHistories()
	{
		return $this->hasMany(LeadHistory::class);
	}

    public function isWaiting()
    {
        return $this->status === 'Waiting';
    }

    public function needsCallAgain()
    {
        return $this->status === 'Call_Again';
    }

    public function hasScheduledCall()
    {
        return $this->status === 'Scheduled_Call';
    }

    public function isRegistered()
    {
        return $this->status === 'Registered';
    }

    public function isNotInterested()
    {
        return $this->status === 'Not_Interested';
    }

    public function isArchived()
    {
        return $this->status === 'Archived';
    }

    public function isActive()
    {
        return $this->is_active === true;
    }

    public function assignTo($employeeId)
    {
        $this->update([
            'owner_cs_id' => $employeeId
        ]);
    }

    public function archive()
    {
        $this->update([
            'status' => 'Archived',
            'is_active' => false
        ]);
    }

    public function scheduleCall($datetime)
    {
        $this->update([
            'status' => 'Scheduled_Call',
            'next_call_at' => $datetime
        ]);
    }

    public function markCallAgain($datetime)
    {
        $this->update([
            'status' => 'Call_Again',
            'next_call_at' => $datetime
        ]);
    }

    public function isCallDue()
    {
        if (!$this->next_call_at) {
            return false;
        }

        return now()->greaterThanOrEqualTo($this->next_call_at);
    }

    public function scopeActive(Builder $query)
    {
        return $query->where('is_active', true);
    }

    public function scopeWaiting(Builder $query)
    {
        return $query->where('status', 'Waiting');
    }

    public function scopeCallAgain(Builder $query)
    {
        return $query->where('status', 'Call_Again');
    }

    public function scopeScheduledCalls(Builder $query)
    {
        return $query->where('status', 'Scheduled_Call');
    }

    public function scopeRegistered(Builder $query)
    {
        return $query->where('status', 'Registered');
    }

    public function scopeDueCalls(Builder $query)
    {
        return $query->whereNotNull('next_call_at')
                     ->where('next_call_at', '<=', now());
    }

    public function scopeOwnedBy(Builder $query, $employeeId)
    {
        return $query->where('owner_cs_id', $employeeId);
    }

    public function age()
    {
        if (!$this->birthdate) {
            return null;
        }

        return $this->birthdate->age;
    }

    public function hasCourseInterest()
    {
        return $this->interested_course_template_id !== null;
    }

    public function lastCall()
    {
        return $this->callLogs()
            ->latest('created_at')
            ->first();
    }
}
