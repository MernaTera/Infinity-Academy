<?php


namespace App\Models\Finance;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use App\Models\HR\Employee;
use App\Models\Enrollment\Enrollment;

/**
 * Class PrivateBundle
 * 
 * @property int $bundle_id
 * @property float $hours
 * @property float $price
 * @property bool $is_active
 * @property int|null $created_by_admin_id
 * @property Carbon|null $created_at
 * 
 * @property Employee|null $employee
 * @property Collection|Enrollment[] $enrollments
 *
 * @package App\Models
 */
class PrivateBundle extends Model
{
	protected $table = 'private_bundle';
	protected $primaryKey = 'bundle_id';
	public $timestamps = true;

	protected $casts = [
		'hours' => 'decimal:2',
		'price' => 'decimal:2',
		'is_active' => 'boolean',
		'created_by_admin_id' => 'integer',
		'created_at' => 'datetime'
	];

	protected $fillable = [
		'hours',
		'price',
		'is_active',
		'created_by_admin_id'
	];

	public function createdBy()
	{
		return $this->belongsTo(Employee::class, 'created_by_admin_id');
	}

	public function enrollments()
	{
		return $this->hasMany(Enrollment::class, 'bundle_id');
	}

    public function isActive()
    {
        return $this->is_active === true;
    }

    public function pricePerHour()
    {
        if ($this->hours == 0) {
            return 0;
        }

        return $this->price / $this->hours;
    }

    public function totalEnrollments()
    {
        return $this->enrollments()->count();
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
