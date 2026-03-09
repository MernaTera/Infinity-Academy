<?php


namespace App\Models\Enrollment;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use App\Models\HR\Employee;
use App\Models\Enrollment\Enrollment;

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
		'enrollment_id' => 'integer',
		'start_date' => 'date',
		'expected_return_date' => 'date',
		'actual_return_date' => 'date',
		'created_by_cs_id' => 'integer',
		'created_at' => 'datetime'
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

	public function createdBy()
	{
		return $this->belongsTo(Employee::class, 'created_by_cs_id');
	}

	public function enrollment()
	{
		return $this->belongsTo(Enrollment::class, 'enrollment_id');
	}

	public function isActive()
	{
		return $this->status === 'Active';
	}

	public function markAsReturned($actualReturnDate = null)
	{
		$this->status = 'Returned';
		$this->actual_return_date = $actualReturnDate ?? Carbon::now();
		$this->save();
	}

	public function isExpired()
	{
		return $this->status === 'Expired';
	}

	public function isReturned()
	{
		return $this->status === 'Returned';
	}
}
