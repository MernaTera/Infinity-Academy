<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Room
 * 
 * @property int $room_id
 * @property int $branch_id
 * @property string|null $name
 * @property int|null $capacity
 * @property string|null $room_type
 * @property bool|null $is_active
 * @property int|null $created_by_admin_id
 * @property Carbon|null $created_at
 * 
 * @property Employee|null $employee
 * @property Branch $branch
 * @property Collection|CourseInstance[] $course_instances
 * @property Collection|Session[] $sessions
 *
 * @package App\Models
 */
class Room extends Model
{
	protected $table = 'room';
	protected $primaryKey = 'room_id';
	public $timestamps = false;

	protected $casts = [
		'branch_id' => 'int',
		'capacity' => 'int',
		'is_active' => 'bool',
		'created_by_admin_id' => 'int'
	];

	protected $fillable = [
		'branch_id',
		'name',
		'capacity',
		'room_type',
		'is_active',
		'created_by_admin_id'
	];

	public function employee()
	{
		return $this->belongsTo(Employee::class, 'created_by_admin_id');
	}

	public function branch()
	{
		return $this->belongsTo(Branch::class);
	}

	public function course_instances()
	{
		return $this->hasMany(CourseInstance::class);
	}

	public function sessions()
	{
		return $this->hasMany(Session::class);
	}
}
