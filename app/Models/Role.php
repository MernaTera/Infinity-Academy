<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

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
		'is_active' => 'bool'
	];

	protected $fillable = [
		'role_name',
		'description',
		'is_active'
	];

	public function permissions()
	{
		return $this->belongsToMany(Permission::class, 'role_permission');
	}

	public function users()
	{
		return $this->hasMany(User::class);
	}
}
