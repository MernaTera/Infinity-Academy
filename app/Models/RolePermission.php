<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class RolePermission
 * 
 * @property int $role_id
 * @property int $permission_id
 * 
 * @property Role $role
 * @property Permission $permission
 *
 * @package App\Models
 */
class RolePermission extends Model
{
	protected $table = 'role_permission';
	public $incrementing = false;
	public $timestamps = false;

	protected $casts = [
		'role_id' => 'int',
		'permission_id' => 'int'
	];

	public function role()
	{
		return $this->belongsTo(Role::class);
	}

	public function permission()
	{
		return $this->belongsTo(Permission::class);
	}
}
