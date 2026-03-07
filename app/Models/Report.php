<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Report
 * 
 * @property int $report_id
 * @property int $enrollment_id
 * @property int $teacher_id
 * @property float|null $total_score
 * @property string $status
 * @property Carbon|null $submitted_at
 * @property Carbon|null $sent_at
 * @property Carbon|null $approved_at
 * @property int|null $approved_by_admin_id
 * @property string|null $rejection_note
 * @property bool|null $pdf_generated
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property Employee|null $employee
 * @property Enrollment $enrollment
 * @property Teacher $teacher
 * @property Collection|ReportScore[] $report_scores
 *
 * @package App\Models
 */
class Report extends Model
{
	protected $table = 'report';
	protected $primaryKey = 'report_id';

	protected $casts = [
		'enrollment_id' => 'int',
		'teacher_id' => 'int',
		'total_score' => 'float',
		'submitted_at' => 'datetime',
		'sent_at' => 'datetime',
		'approved_at' => 'datetime',
		'approved_by_admin_id' => 'int',
		'pdf_generated' => 'bool'
	];

	protected $fillable = [
		'enrollment_id',
		'teacher_id',
		'total_score',
		'status',
		'submitted_at',
		'sent_at',
		'approved_at',
		'approved_by_admin_id',
		'rejection_note',
		'pdf_generated'
	];

	public function employee()
	{
		return $this->belongsTo(Employee::class, 'approved_by_admin_id');
	}

	public function enrollment()
	{
		return $this->belongsTo(Enrollment::class);
	}

	public function teacher()
	{
		return $this->belongsTo(Teacher::class);
	}

	public function report_scores()
	{
		return $this->hasMany(ReportScore::class);
	}
}
