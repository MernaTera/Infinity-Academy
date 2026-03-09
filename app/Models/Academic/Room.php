<?php



namespace App\Models\Academic;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use App\Models\Auth\Employee;
use App\Models\Core\Branch;
use App\Models\Academic\CourseInstance;
use App\Models\Academic\CourseSession;

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
		'branch_id' => 'integer',
		'capacity' => 'integer',
		'is_active' => 'boolean',
		'created_by_admin_id' => 'integer',
		'created_at' => 'datetime'
	];

	protected $fillable = [
		'branch_id',
		'name',
		'capacity',
		'room_type',
		'is_active',
		'created_by_admin_id'
	];

	public function createdByAdmin()
	{
		return $this->belongsTo(Employee::class, 'created_by_admin_id');
	}

	public function branch()
	{
		return $this->belongsTo(Branch::class, 'branch_id');
	}

	public function courseInstances()
	{
		return $this->hasMany(CourseInstance::class, 'room_id');
	}

	public function sessions()
	{
		return $this->hasMany(Session::class, 'room_id');
	}

	public function isOnline()
	{
		return $this->room_type === 'online';
	}

	public function isOffline()
	{
		return $this->room_type === 'offline';
	}

	public function isActive()
	{
		return $this->is_active === true;
	}	
}
