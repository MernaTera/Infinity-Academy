<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class BundleUsageLog
 * 
 * @property int $usage_id
 * @property int $enrollment_id
 * @property int $session_id
 * @property float $hours_deducted
 * @property string|null $reason
 * @property int|null $created_by_cs_id
 * @property Carbon|null $created_at
 * 
 * @property Employee|null $employee
 * @property Enrollment $enrollment
 * @property Session $session
 *
 * @package App\Models
 */
class BundleUsageLog extends Model
{
	protected $table = 'bundle_usage_log';
	protected $primaryKey = 'usage_id';
	public $timestamps = false;

	protected $casts = [
		'enrollment_id' => 'int',
		'session_id' => 'int',
		'hours_deducted' => 'float',
		'created_by_cs_id' => 'int'
	];

	protected $fillable = [
		'enrollment_id',
		'session_id',
		'hours_deducted',
		'reason',
		'created_by_cs_id'
	];

	public function employee()
	{
		return $this->belongsTo(Employee::class, 'created_by_cs_id');
	}

	public function enrollment()
	{
		return $this->belongsTo(Enrollment::class);
	}

	public function session()
	{
		return $this->belongsTo(Session::class);
	}
}
