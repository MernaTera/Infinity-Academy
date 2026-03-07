<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Module
 * 
 * @property int $module_id
 * @property string|null $module_name
 * @property Carbon|null $created_at
 * 
 * @property Collection|Permission[] $permissions
 *
 * @package App\Models
 */
class Module extends Model
{
	protected $table = 'module';
	protected $primaryKey = 'module_id';
	public $timestamps = false;

	protected $fillable = [
		'module_name'
	];

	public function permissions()
	{
		return $this->hasMany(Permission::class);
	}
}
