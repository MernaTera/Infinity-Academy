<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Student
 * 
 * @property int $student_id
 * @property int|null $user_id
 * @property string|null $full_name
 * @property Carbon|null $birthdate
 * @property string|null $degree
 * @property string|null $location
 * @property string|null $email
 * @property string|null $status
 * @property bool|null $is_active
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property User|null $user
 * @property Collection|Enrollment[] $enrollments
 * @property Collection|PlacementTest[] $placement_tests
 * @property Collection|StudentPhone[] $student_phones
 *
 * @package App\Models
 */
class Student extends Model
{
	protected $table = 'student';
	protected $primaryKey = 'student_id';

	protected $casts = [
		'user_id' => 'int',
		'birthdate' => 'datetime',
		'is_active' => 'bool'
	];

	protected $fillable = [
		'user_id',
		'full_name',
		'birthdate',
		'degree',
		'location',
		'email',
		'status',
		'is_active'
	];

	public function user()
	{
		return $this->belongsTo(User::class);
	}

	public function enrollments()
	{
		return $this->hasMany(Enrollment::class);
	}

	public function placement_tests()
	{
		return $this->hasMany(PlacementTest::class);
	}

	public function student_phones()
	{
		return $this->hasMany(StudentPhone::class);
	}
}
