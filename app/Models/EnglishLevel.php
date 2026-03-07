<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class EnglishLevel
 * 
 * @property int $english_level_id
 * @property string $level_name
 * @property int $level_rank
 * @property Carbon|null $created_at
 * 
 * @property Collection|Level[] $levels
 * @property Collection|Sublevel[] $sublevels
 * @property Collection|Teacher[] $teachers
 *
 * @package App\Models
 */
class EnglishLevel extends Model
{
	protected $table = 'english_level';
	protected $primaryKey = 'english_level_id';
	public $timestamps = false;

	protected $casts = [
		'level_rank' => 'int'
	];

	protected $fillable = [
		'level_name',
		'level_rank'
	];

	public function levels()
	{
		return $this->hasMany(Level::class, 'teacher_level');
	}

	public function sublevels()
	{
		return $this->hasMany(Sublevel::class, 'teacher_min_level');
	}

	public function teachers()
	{
		return $this->hasMany(Teacher::class);
	}
}
