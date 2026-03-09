<?php


namespace App\Models\Auth;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use App\Models\Auth\Permission;
use App\Models\Auth\User;

/**
 * Class Role
 * 
 * @property int $role_id
 * @property string|null $role_name
 * @property string|null $description
 * @property bool|null $is_active
 * @property Carbon|null $created_at
 * 
 * @property Collection|Permission[] $permissions
 * @property Collection|User[] $users
 *
 * @package App\Models
 */
class Role extends Model
{
	protected $table = 'role';
	protected $primaryKey = 'role_id';
	public $timestamps = false;

	protected $casts = [
		'is_active' => 'boolean',
		'created_at' => 'datetime'
	];

	protected $fillable = [
		'role_name',
		'description',
		'is_active'
	];

	public function permissions()
	{
		return $this->belongsToMany(Permission::class, 'role_permission', 'role_id', 'permission_id');
	}

	public function users()
	{
		return $this->hasMany(User::class, 'role_id');
	}

	public function scopeActive($query)
	{
		return $query->where('is_active', true);
	}

	public function hasPermission($permissionKey)
	{
		return $this->permissions()
			->where('permission_key', $permissionKey)
			->exists();
	}

	public function can($module, $action)
	{
		return $this->permissions()
			->whereHas('module', function ($q) use ($module) {
				$q->where('module_name', $module);
			})
			->where('action_type', $action)
			->exists();
	}
}
