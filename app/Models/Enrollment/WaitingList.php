<?php


namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use App\Models\HR\Employee;
use App\Models\Enrollment\Enrollment;
use App\Models\Academic\Patch;

/**
 * Class WaitingList
 * 
 * @property int $waiting_id
 * @property int|null $enrollment_id
 * @property int|null $requested_patch_id
 * @property string|null $preferred_type
 * @property string|null $preferred_delivery_mood
 * @property Carbon|null $preferred_start_date
 * @property string|null $status
 * @property int|null $created_by_cs_id
 * @property Carbon|null $created_at
 * 
 * @property Employee|null $employee
 * @property Enrollment|null $enrollment
 * @property Patch|null $patch
 *
 * @package App\Models
 */
class WaitingList extends Model
{
	protected $table = 'waiting_list';
	protected $primaryKey = 'waiting_id';
	public $timestamps = false;

	protected $casts = [
		'enrollment_id' => 'integer',
		'requested_patch_id' => 'integer',
		'preferred_start_date' => 'datetime',
		'created_by_cs_id' => 'integer',
		'created_at' => 'datetime'
	];

	protected $fillable = [
		'enrollment_id',
		'requested_patch_id',
		'preferred_type',
		'preferred_delivery_mood',
		'preferred_start_date',
		'status',
		'created_by_cs_id'
	];

	public function createdBy()
	{
		return $this->belongsTo(Employee::class, 'created_by_cs_id');
	}

	public function enrollment()
	{
		return $this->belongsTo(Enrollment::class, 'enrollment_id');
	}

	public function patch()
	{
		return $this->belongsTo(Patch::class, 'requested_patch_id');
	}

	public function isActive()
	{
		return $this->status === 'Active';
	}

	public function isMoved()
	{
		return $this->status === 'Moved';
	}

	public function isCancelled()
	{
		return $this->status === 'Cancelled';
	}

	public function wantsNextPatch()
	{
		return $this->preferred_type === 'Next_Patch';
	}
	
	public function wantsSpecificdate()
	{
		return $this->preferred_type === 'Specific_Date';
	}

	public function wantsOnline()
	{
		return $this->preferred_delivery_mood === 'Online';
	}

	public function wantsOffline()
	{
		return $this->preferred_delivery_mood === 'Offline';
	}

}
