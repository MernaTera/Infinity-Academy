<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class RestrictionLog
 * 
 * @property int $restriction_id
 * @property int $enrollment_id
 * @property string $triggered_by
 * @property string $reason
 * @property Carbon $triggered_at
 * @property Carbon|null $released_at
 * @property int|null $released_by
 * @property string|null $notes
 * 
 * @property Employee|null $employee
 * @property Enrollment $enrollment
 *
 * @package App\Models
 */
class RestrictionLog extends Model
{
	protected $table = 'restriction_log';
	protected $primaryKey = 'restriction_id';
	public $timestamps = false;

	protected $casts = [
		'enrollment_id' => 'int',
		'triggered_at' => 'datetime',
		'released_at' => 'datetime',
		'released_by' => 'int'
	];

	protected $fillable = [
		'enrollment_id',
		'triggered_by',
		'reason',
		'triggered_at',
		'released_at',
		'released_by',
		'notes'
	];

	public function employee()
	{
		return $this->belongsTo(Employee::class, 'released_by');
	}

	public function enrollment()
	{
		return $this->belongsTo(Enrollment::class);
	}
}
