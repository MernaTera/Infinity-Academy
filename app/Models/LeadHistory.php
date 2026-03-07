<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

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
		'lead_id' => 'int',
		'changed_by' => 'int',
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
		return $this->belongsTo(Lead::class);
	}
}
