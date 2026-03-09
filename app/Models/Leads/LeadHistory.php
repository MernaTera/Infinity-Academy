<?php



namespace App\Models\Leads;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use App\Models\HR\Employee;
use App\Models\Lead\Lead;

/**
 * Class LeadHistory
 * 
 * @property int $history_id
 * @property int $lead_id
 * @property string|null $old_status
 * @property string|null $new_status
 * @property string|null $notes
 * @property int $changed_by
 * @property Carbon $changed_at
 * 
 * @property Employee $employee
 * @property Lead $lead
 *
 * @package App\Models
 */
class LeadHistory extends Model
{
	protected $table = 'lead_history';
	protected $primaryKey = 'history_id';
	public $timestamps = false;

	protected $casts = [
		'lead_id' => 'integer',
		'changed_by' => 'integer',
		'changed_at' => 'datetime'
	];

	protected $fillable = [
		'lead_id',
		'old_status',
		'new_status',
		'notes',
		'changed_by',
		'changed_at'
	];

	public function employee()
	{
		return $this->belongsTo(Employee::class, 'changed_by');
	}

	public function lead()
	{
		return $this->belongsTo(Lead::class, 'lead_id');
	}

    public function isRegistration()
    {
        return $this->new_status === 'Registered';
    }

    public function isArchived()
    {
        return $this->new_status === 'Archived';
    }

    public function isCallAgain()
    {
        return $this->new_status === 'Call_Again';
    }

    public function isScheduledCall()
    {
        return $this->new_status === 'Scheduled_Call';
    }

    public function scopeForLead(Builder $query, $leadId)
    {
        return $query->where('lead_id', $leadId);
    }

    public function scopeRegistrations(Builder $query)
    {
        return $query->where('new_status', 'Registered');
    }

    public function scopeArchived(Builder $query)
    {
        return $query->where('new_status', 'Archived');
    }

    public function scopeByEmployee(Builder $query, $employeeId)
    {
        return $query->where('changed_by', $employeeId);
    }

    public function scopeToday(Builder $query)
    {
        return $query->whereDate('changed_at', today());
    }

    public function isStatusChange()
    {
        return $this->old_status !== $this->new_status;
    }

    public function changeSummary()
    {
        return "{$this->old_status} → {$this->new_status}";
    }

    public function changeAge()
    {
        if (!$this->changed_at) {
            return null;
        }

        return $this->changed_at->diffForHumans();
    }
}
