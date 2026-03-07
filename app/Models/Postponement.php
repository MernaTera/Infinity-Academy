<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Postponement
 * 
 * @property int $postponement_id
 * @property int $enrollment_id
 * @property Carbon $start_date
 * @property Carbon $expected_return_date
 * @property Carbon|null $actual_return_date
 * @property string $status
 * @property string|null $reason
 * @property int|null $created_by_cs_id
 * @property Carbon|null $created_at
 * 
 * @property Employee|null $employee
 * @property Enrollment $enrollment
 *
 * @package App\Models
 */
class Postponement extends Model
{
	protected $table = 'postponement';
	protected $primaryKey = 'postponement_id';
	public $timestamps = false;

	protected $casts = [
		'enrollment_id' => 'int',
		'start_date' => 'datetime',
		'expected_return_date' => 'datetime',
		'actual_return_date' => 'datetime',
		'created_by_cs_id' => 'int'
	];

	protected $fillable = [
		'enrollment_id',
		'start_date',
		'expected_return_date',
		'actual_return_date',
		'status',
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
}
