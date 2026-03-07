<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class ReportScore
 * 
 * @property int $score_id
 * @property int $report_id
 * @property string $component_name
 * @property float $max_score
 * @property float $student_score
 * @property Carbon|null $created_at
 * 
 * @property Report $report
 *
 * @package App\Models
 */
class ReportScore extends Model
{
	protected $table = 'report_score';
	protected $primaryKey = 'score_id';
	public $timestamps = false;

	protected $casts = [
		'report_id' => 'int',
		'max_score' => 'float',
		'student_score' => 'float'
	];

	protected $fillable = [
		'report_id',
		'component_name',
		'max_score',
		'student_score'
	];

	public function report()
	{
		return $this->belongsTo(Report::class);
	}
}
