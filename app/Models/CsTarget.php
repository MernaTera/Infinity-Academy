<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class CsTarget
 * 
 * @property int $target_id
 * @property int $employee_id
 * @property int $patch_id
 * @property float|null $target_amount
 * @property int|null $target_registrations
 * @property bool|null $is_locked
 * @property int $created_by_admin_id
 * @property Carbon|null $created_at
 * 
 * @property Employee $employee
 * @property Patch $patch
 *
 * @package App\Models
 */
class CsTarget extends Model
{
	protected $table = 'cs_target';
	protected $primaryKey = 'target_id';
	public $timestamps = false;

	protected $casts = [
		'employee_id' => 'int',
		'patch_id' => 'int',
		'target_amount' => 'float',
		'target_registrations' => 'int',
		'is_locked' => 'bool',
		'created_by_admin_id' => 'int'
	];

	protected $fillable = [
		'employee_id',
		'patch_id',
		'target_amount',
		'target_registrations',
		'is_locked',
		'created_by_admin_id'
	];

	public function employee()
	{
		return $this->belongsTo(Employee::class);
	}

	public function patch()
	{
		return $this->belongsTo(Patch::class);
	}
}
