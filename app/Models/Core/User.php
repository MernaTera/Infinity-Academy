<?php


namespace App\Models\Core;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;

/**
 * Class User
 * 
 * @property int $id
 * @property string|null $username
 * @property string $name
 * @property string $email
 * @property Carbon|null $email_verified_at
 * @property string $password
 * @property string|null $remember_token
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property int|null $role_id
 * @property bool $is_active
 * @property int $failed_attempts
 * @property Carbon|null $locked_until
 * @property Carbon|null $last_login_at
 * 
 * @property Role|null $role
 * @property Collection|Employee[] $employees
 * @property Collection|Student[] $students
 *
 * @package App\Models
 */
class User extends Authenticatable
{
	protected $table = 'users';

	protected $casts = [
		'email_verified_at' => 'datetime',
		'role_id' => 'integer',
		'is_active' => 'boolean',
		'failed_attempts' => 'integer',
		'locked_until' => 'datetime',
		'last_login_at' => 'datetime'
	];

	protected $hidden = [
		'password',
		'remember_token'
	];

	protected $fillable = [
		'username',
		'name',
		'email',
		'email_verified_at',
		'password',
		'remember_token',
		'role_id',
		'is_active',
		'failed_attempts',
		'locked_until',
		'last_login_at'
	];

	public function role()
	{
		return $this->belongsTo(Role::class, 'role_id');
	}

	public function employees()
	{
		return $this->hasMany(Employee::class, 'user_id');
	}

	public function students()
	{
		return $this->hasMany(Student::class, 'user_id');
	}

	public function hasRole($roleName)
	{
		return $this->role && $this->role->role_name === $roleName;
	}

	public function canDo($permissionKey)
	{
		return $this->role
			? $this->role->hasPermission($permissionKey)
			: false;
	}

	public function isLocked()
	{
		return $this->locked_until && now()->lessThan($this->locked_until);
	}

	public function recordFailedLogin()
	{
		$this->increment('failed_attempts');

		if ($this->failed_attempts >= 5) {
			$this->update([
				'locked_until' => now()->addMinutes(15)
			]);
		}
	}

	public function scopeActive($query)
	{
		return $query->where('is_active', true);
	}

}
