<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Permission
 * 
 * @property int $permission_id
 * @property int $module_id
 * @property string $action_type
 * @property string $permission_key
 * @property Carbon $created_at
 * 
 * @property Module $module
 * @property Collection|Role[] $roles
 *
 * @package App\Models
 */
class Permission extends Model
{
	protected $table = 'permission';
	protected $primaryKey = 'permission_id';
	public $timestamps = false;

	protected $casts = [
		'module_id' => 'int'
	];

	protected $fillable = [
		'module_id',
		'action_type',
		'permission_key'
	];

	public function module()
	{
		return $this->belongsTo(Module::class);
	}

	public function roles()
	{
		return $this->belongsToMany(Role::class, 'role_permission');
	}
}
