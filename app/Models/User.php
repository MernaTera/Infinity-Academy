<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

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
class User extends Model
{
	protected $table = 'users';

	protected $casts = [
		'email_verified_at' => 'datetime',
		'role_id' => 'int',
		'is_active' => 'bool',
		'failed_attempts' => 'int',
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
		return $this->belongsTo(Role::class);
	}

	public function employees()
	{
		return $this->hasMany(Employee::class);
	}

	public function students()
	{
		return $this->hasMany(Student::class);
	}
}
