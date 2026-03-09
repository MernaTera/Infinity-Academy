<?php


namespace App\Models\Reports;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use App\Models\Reports\Report;
use App\Models\HR\Teacher;

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
		'report_id' => 'integer',
		'max_score' => 'float',
		'student_score' => 'float',
		'created_at' => 'datetime'
	];

	protected $fillable = [
		'report_id',
		'component_name',
		'max_score',
		'student_score'
	];

	public function report()
	{
		return $this->belongsTo(Report::class, 'report_id');
	}

    public function percentage()
    {
        if ($this->max_score == 0) {
            return 0;
        }

        return round(($this->student_score / $this->max_score) * 100, 2);
    }

    public function isFullScore()
    {
        return $this->student_score == $this->max_score;
    }

    public function isFail()
    {
        return $this->percentage() < 50;
    }

    public function remainingScore()
    {
        return $this->max_score - $this->student_score;
    }

    public function scopeForReport(Builder $query, $reportId)
    {
        return $query->where('report_id', $reportId);
    }

    public function scopeComponent(Builder $query, $component)
    {
        return $query->where('component_name', $component);
    }

    public function scopeHighScores(Builder $query)
    {
        return $query->whereColumn('student_score', '>=', 'max_score');
    }

    public function scoreSummary()
    {
        return "{$this->student_score} / {$this->max_score}";
    }

    public function grade()
    {
        $percent = $this->percentage();

        return match(true) {
            $percent >= 90 => 'A',
            $percent >= 80 => 'B',
            $percent >= 70 => 'C',
            $percent >= 60 => 'D',
            default => 'F'
        };
    }
}
