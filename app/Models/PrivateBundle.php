<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

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
	public $timestamps = false;

	protected $casts = [
		'hours' => 'float',
		'price' => 'float',
		'is_active' => 'bool',
		'created_by_admin_id' => 'int'
	];

	protected $fillable = [
		'hours',
		'price',
		'is_active',
		'created_by_admin_id'
	];

	public function employee()
	{
		return $this->belongsTo(Employee::class, 'created_by_admin_id');
	}

	public function enrollments()
	{
		return $this->hasMany(Enrollment::class, 'bundle_id');
	}
}
