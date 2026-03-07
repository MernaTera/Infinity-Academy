<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

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
		'discount_value' => 'float',
		'start_date' => 'datetime',
		'end_date' => 'datetime',
		'is_active' => 'bool',
		'created_by_admin_id' => 'int'
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

	public function employee()
	{
		return $this->belongsTo(Employee::class, 'created_by_admin_id');
	}

	public function course_templates()
	{
		return $this->belongsToMany(CourseTemplate::class, 'offer_course_template');
	}
}
