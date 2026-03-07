<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class ContractType
 * 
 * @property int $contract_id
 * @property int $teacher_id
 * @property int $patch_id
 * @property string $contract_type
 * @property int $max_sessions_allowed
 * @property bool|null $is_active
 * @property int $created_by_admin_id
 * @property Carbon|null $created_at
 * 
 * @property Employee $employee
 * @property Patch $patch
 * @property Teacher $teacher
 *
 * @package App\Models
 */
class ContractType extends Model
{
	protected $table = 'contract_type';
	protected $primaryKey = 'contract_id';
	public $timestamps = false;

	protected $casts = [
		'teacher_id' => 'int',
		'patch_id' => 'int',
		'max_sessions_allowed' => 'int',
		'is_active' => 'bool',
		'created_by_admin_id' => 'int'
	];

	protected $fillable = [
		'teacher_id',
		'patch_id',
		'contract_type',
		'max_sessions_allowed',
		'is_active',
		'created_by_admin_id'
	];

	public function employee()
	{
		return $this->belongsTo(Employee::class, 'created_by_admin_id');
	}

	public function patch()
	{
		return $this->belongsTo(Patch::class);
	}

	public function teacher()
	{
		return $this->belongsTo(Teacher::class);
	}
}
