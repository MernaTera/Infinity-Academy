<?php


namespace App\Models\Academic;

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
		'level_name' => 'string',
		'level_rank' => 'integer',
		'created_at' => 'datetime'
	];

	protected $fillable = [
		'level_name',
		'level_rank'
	];

	public function teachers()
	{
		return $this->hasMany(Teacher::class, 'english_level_id');
	}

	public function scopeOrdered($query)
	{
		return $query->orderBy('level_rank');
	}

	public function isHigherThan($level)
	{
		return $this->level_rank > $level->level_rank;
	}
}
