<?php


namespace App\Models\Auth;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use App\Models\Auth\Permission;

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
		return $this->hasMany(Permission::class, 'module_id');
	}
	
	public function scopeWithPermissions($query)
	{
    	return $query->with('permissions');
	}

	public function scopeActive($query)
	{
		return $query->where('is_active', true);
	}
}
