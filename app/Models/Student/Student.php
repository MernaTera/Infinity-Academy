<?php


namespace App\Models\Student;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;


use App\Models\Auth\User;
use App\Models\Enrollment\Enrollment;
use App\Models\Enrollment\PlacementTest;
use App\Models\Student\StudentPhone;


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
	public $timestamps = true;

	protected $casts = [
		'user_id' => 'integer',
		'birthdate' => 'datetime',
		'is_active' => 'boolean',
		'created_at' => 'datetime',
		'updated_at' => 'datetime'
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
		return $this->belongsTo(User::class, 'user_id');
	}

	public function enrollments()
	{
		return $this->hasMany(Enrollment::class, 'student_id');
	}

	public function placement_tests()
	{
		return $this->hasMany(PlacementTest::class, 'student_id');
	}

	public function student_phones()
	{
		return $this->hasMany(StudentPhone::class, 'student_id');
	}

    public function isActive()
    {
        return $this->status === 'Active';
    }

    public function isArchived()
    {
        return $this->status === 'Archived';
    }

    public function isDropped()
    {
        return $this->status === 'Dropped';
    }

    public function archive()
    {
        $this->update([
            'status' => 'Archived',
            'is_active' => false
        ]);
    }

    public function activate()
    {
        $this->update([
            'status' => 'Active',
            'is_active' => true
        ]);
    }

    public function drop()
    {
        $this->update([
            'status' => 'Dropped',
            'is_active' => false
        ]);
    }

    public function scopeActive(Builder $query)
    {
        return $query->where('status', 'Active');
    }

    public function scopeArchived(Builder $query)
    {
        return $query->where('status', 'Archived');
    }

    public function scopeDropped(Builder $query)
    {
        return $query->where('status', 'Dropped');
    }

    public function scopeSearch(Builder $query, $term)
    {
        return $query->where('full_name', 'like', "%{$term}%")
                     ->orWhere('email', 'like', "%{$term}%");
    }

    public function age()
    {
        if (!$this->birthdate) {
            return null;
        }

        return $this->birthdate->age;
    }

    public function primaryPhone()
    {
        return $this->student_phones()->first();
    }

    public function activeEnrollment()
    {
        return $this->enrollments()
            ->where('status', 'Active')
            ->first();
    }

    public function lastEnrollment()
    {
        return $this->enrollments()
            ->latest('created_at')
            ->first();
    }

}
