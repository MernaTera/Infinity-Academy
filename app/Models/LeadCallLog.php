<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

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
		'lead_id' => 'int',
		'cs_id' => 'int',
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
		return $this->belongsTo(Lead::class);
	}
}
