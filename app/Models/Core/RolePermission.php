<?php


namespace App\Models\Core;

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
	protected $primaryKey = null;
	public $incrementing = false;
	public $timestamps = false;

	protected $casts = [
		'role_id' => 'int',
		'permission_id' => 'int'
	];

	protected $fillable = [
		'role_id' => 'int',
		'permission_id' => 'int'
	];

	public function role()
	{
		return $this->belongsTo(Role::class, 'role_id');
	}

	public function permission()
	{
		return $this->belongsTo(Permission::class, 'permission_id');
	}
}
