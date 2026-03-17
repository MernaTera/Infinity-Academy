<?php


namespace App\Models\Auth;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use App\Models\Auth\Role;
use App\Models\HR\Employee;
use App\Models\Student\Student;

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
		return $this->hasOne(Employee::class, 'user_id');
	}

	public function students()
	{
		return $this->hasOne(Student::class, 'user_id');
	}

	public function hasRole($roleName)
	{
		return $this->role && $this->role->role_name === $roleName;
	}

	public function canDo($permissionKey)
	{
		if (!$this->role) {
			return false;
		}

		if (!$this->relationLoaded('role')) {
			$this->load('role.permissions');
		}

		return $this->role->permissions
			->contains('permission_key', $permissionKey);
	}

	public function isLocked()
	{
		return $this->locked_until && $this->locked_until->isFuture();
	}

	public function recordFailedLogin()
	{
		$this->failed_attempts++;

		if ($this->failed_attempts >= 5) {
			$this->locked_until = now()->addMinutes(15);
		}

		$this->save();
	}

	public function scopeActive($query)
	{
		return $query->where('is_active', true);
	}

	public function isAdmin()
	{
		return $this->hasRole('Admin');
	}

	public function isCS()
	{
		return $this->hasRole('CS');
	}

	public function isTeacher()
	{
		return $this->hasRole('Teacher');
	}

	public function isStudent()
	{
		return $this->hasRole('Student');
	}

	public function isActive()
	{
		return $this->is_active === true;
	}

}
