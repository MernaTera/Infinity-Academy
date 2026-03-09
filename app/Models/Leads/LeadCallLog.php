<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use App\Models\HR\Employee;
use App\Models\Lead\Lead;

/**
 * Class LeadCallLog
 * 
 * @property int $call_id
 * @property int|null $lead_id
 * @property int|null $cs_id
 * @property Carbon|null $call_datetime
 * @property string|null $outcome
 * @property string|null $notes
 * 
 * @property Employee|null $employee
 * @property Lead|null $lead
 *
 * @package App\Models
 */
class LeadCallLog extends Model
{
	protected $table = 'lead_call_log';
	protected $primaryKey = 'call_id';
	public $timestamps = false;

	protected $casts = [
		'lead_id' => 'integer',
		'cs_id' => 'integer',
		'call_datetime' => 'datetime'
	];

	protected $fillable = [
		'lead_id',
		'cs_id',
		'call_datetime',
		'outcome',
		'notes'
	];

	public function employee()
	{
		return $this->belongsTo(Employee::class, 'cs_id');
	}

	public function lead()
	{
		return $this->belongsTo(Lead::class, 'lead_id');
	}

    public function isInterested()
    {
        return $this->outcome === 'Interested';
    }

    public function isRegistered()
    {
        return $this->outcome === 'Registered';
    }

    public function isNotInterested()
    {
        return $this->outcome === 'Not_Interested';
    }

    public function needsCallAgain()
    {
        return $this->outcome === 'Call_Again';
    }

    public function isNoAnswer()
    {
        return $this->outcome === 'No_Answer';
    }

    public function isWrongNumber()
    {
        return $this->outcome === 'Wrong_Number';
    }

    public function isFollowUpScheduled()
    {
        return $this->outcome === 'Follow_Up_Scheduled';
    }

    public function scopeInterested(Builder $query)
    {
        return $query->where('outcome', 'Interested');
    }

    public function scopeRegistered(Builder $query)
    {
        return $query->where('outcome', 'Registered');
    }

    public function scopeNoAnswer(Builder $query)
    {
        return $query->where('outcome', 'No_Answer');
    }

    public function scopeCallAgain(Builder $query)
    {
        return $query->where('outcome', 'Call_Again');
    }

    public function scopeToday(Builder $query)
    {
        return $query->whereDate('call_datetime', today());
    }

    public function scopeByCS(Builder $query, $employeeId)
    {
        return $query->where('cs_id', $employeeId);
    }

    public function isToday()
    {
        if (!$this->call_datetime) {
            return false;
        }

        return $this->call_datetime->isToday();
    }

    public function callAge()
    {
        if (!$this->call_datetime) {
            return null;
        }

        return $this->call_datetime->diffForHumans();
    }
}
