<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

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
		'start_time' => 'datetime',
		'end_time' => 'datetime',
		'is_active' => 'bool',
		'created_by_admin_id' => 'int'
	];

	protected $fillable = [
		'name',
		'start_time',
		'end_time',
		'is_active',
		'created_by_admin_id'
	];

	public function employee()
	{
		return $this->belongsTo(Employee::class, 'created_by_admin_id');
	}
}
