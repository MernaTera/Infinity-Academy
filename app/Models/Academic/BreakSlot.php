<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\Academic;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use App\Models\HR\Employee;

/**
 * Class BreakSlot
 * 
 * @property int $break_slot_id
 * @property string|null $name
 * @property Carbon $start_time
 * @property Carbon $end_time
 * @property bool|null $is_active
 * @property int|null $created_by_admin_id
 * @property Carbon|null $created_at
 * 
 * @property Employee|null $employee
 *
 * @package App\Models
 */
class BreakSlot extends Model
{
	protected $table = 'break_slot';
	protected $primaryKey = 'break_slot_id';
	public $timestamps = false;

	protected $casts = [
		'start_time' => 'string',
		'end_time' => 'string',
		'is_active' => 'boolean',
		'created_by_admin_id' => 'integer',
		'created_at' => 'datetime'
	];

	protected $fillable = [
		'name',
		'start_time',
		'end_time',
		'is_active',
		'created_by_admin_id'
	];

	public function createdByAdmin()
	{
		return $this->belongsTo(Employee::class, 'created_by_admin_id');
	}

	public function durationMinutes()
	{
		return Carbon::parse($this->end_time)->diffInMinutes(Carbon::parse($this->start_time));
	}

	public function isActive()
	{
		return $this->is_active === true;
	}

	public function overlapsWith($otherStartTime, $otherEndTime)
	{
		$breakStart = Carbon::parse($this->start_time);
		$breakEnd = Carbon::parse($this->end_time);
		$otherStart = Carbon::parse($otherStartTime);
		$otherEnd = Carbon::parse($otherEndTime);

		return $breakStart->lt($otherEnd) && $breakEnd->gt($otherStart);
	}

	public function scopeActive($query)
	{
		return $query->where('is_active', true);
	}
}
