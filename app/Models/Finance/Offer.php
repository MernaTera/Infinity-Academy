<?php


namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use App\Models\HR\Employee;
use App\Models\Academic\CourseTemplate;

/**
 * Class Offer
 * 
 * @property int $offer_id
 * @property string $offer_name
 * @property string $discount_type
 * @property float $discount_value
 * @property Carbon|null $start_date
 * @property Carbon|null $end_date
 * @property bool|null $is_active
 * @property int|null $created_by_admin_id
 * @property Carbon|null $created_at
 * 
 * @property Employee|null $employee
 * @property Collection|CourseTemplate[] $course_templates
 *
 * @package App\Models
 */
class Offer extends Model
{
	protected $table = 'offer';
	protected $primaryKey = 'offer_id';
	public $timestamps = false;

	protected $casts = [
		'discount_value' => 'decimal:2',
		'start_date' => 'date',
		'end_date' => 'date',
		'is_active' => 'boolean',
		'created_by_admin_id' => 'integer',
		'created_at' => 'datetime'
	];

	protected $fillable = [
		'offer_name',
		'discount_type',
		'discount_value',
		'start_date',
		'end_date',
		'is_active',
		'created_by_admin_id'
	];

	public function createdBy()
	{
		return $this->belongsTo(Employee::class, 'created_by_admin_id');
	}

	public function courseTemplates()
	{
		return $this->belongsToMany(CourseTemplate::class, 'offer_course_template', 'offer_id', 'course_template_id');
	}

    public function isPercentage()
    {
        return $this->discount_type === 'Percentage';
    }

    public function isFixed()
    {
        return $this->discount_type === 'Fixed';
    }

    public function isActive()
    {
        if (!$this->is_active) {
            return false;
        }

        $today = now()->toDateString();

        if ($this->start_date && $today < $this->start_date) {
            return false;
        }

        if ($this->end_date && $today > $this->end_date) {
            return false;
        }

        return true;
    }

    public function calculateDiscount($price)
    {
        if ($this->isPercentage()) {
            return ($price * $this->discount_value) / 100;
        }

        return min($this->discount_value, $price);
    }

    public function applyDiscount($price)
    {
        $discount = $this->calculateDiscount($price);

        return max(0, $price - $discount);
    }

    public function scopeActive($query)
    {
        return $query
            ->where('is_active', true)
            ->where(function ($q) {
                $q->whereNull('start_date')
                  ->orWhereDate('start_date', '<=', now());
            })
            ->where(function ($q) {
                $q->whereNull('end_date')
                  ->orWhereDate('end_date', '>=', now());
            });
    }
}
