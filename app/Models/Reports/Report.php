<?php

namespace App\Models\Reports;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use App\Models\HR\Employee;
use App\Models\Enrollment\Enrollment;
use App\Models\HR\Teacher;
use App\Models\Reports\ReportScore;
use Illuminate\Database\Eloquent\Builder;

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
    public $timestamps = true;

	protected $casts = [
		'enrollment_id' => 'integer',
		'teacher_id' => 'integer',
		'total_score' => 'decimal:2',
		'submitted_at' => 'datetime',
		'sent_at' => 'datetime',
		'approved_at' => 'datetime',
		'approved_by_admin_id' => 'integer',
		'pdf_generated' => 'bool',
		'created_at' => 'datetime',
		'updated_at' => 'datetime'
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

	public function approvedBy()
	{
		return $this->belongsTo(Employee::class, 'approved_by_admin_id');
	}

	public function enrollment()
	{
		return $this->belongsTo(Enrollment::class, 'enrollment_id');
	}

	public function teacher()
	{
		return $this->belongsTo(Teacher::class, 'teacher_id');
	}

	public function reportScores()
	{
		return $this->hasMany(ReportScore::class, 'report_id');
	}

    public function isDraft()
    {
        return $this->status === 'Draft';
    }

    public function isSubmitted()
    {
        return $this->status === 'Submitted';
    }

    public function isApproved()
    {
        return $this->status === 'Approved';
    }

    public function isRejected()
    {
        return $this->status === 'Rejected';
    }

    public function isSent()
    {
        return $this->status === 'Sent';
    }

    public function submit()
    {
        $this->update([
            'status' => 'Submitted',
            'submitted_at' => now()
        ]);
    }

    public function approve($adminId)
    {
        $this->update([
            'status' => 'Approved',
            'approved_at' => now(),
            'approved_by_admin_id' => $adminId
        ]);
    }

    public function reject($adminId, $note)
    {
        $this->update([
            'status' => 'Rejected',
            'approved_by_admin_id' => $adminId,
            'rejection_note' => $note
        ]);
    }

    public function markSent()
    {
        $this->update([
            'status' => 'Sent',
            'sent_at' => now()
        ]);
    }

    public function markPdfGenerated()
    {
        $this->update([
            'pdf_generated' => true
        ]);
    }

    public function scopeDraft(Builder $query)
    {
        return $query->where('status', 'Draft');
    }

    public function scopeSubmitted(Builder $query)
    {
        return $query->where('status', 'Submitted');
    }

    public function scopeApproved(Builder $query)
    {
        return $query->where('status', 'Approved');
    }

    public function scopeRejected(Builder $query)
    {
        return $query->where('status', 'Rejected');
    }

    public function scopeSent(Builder $query)
    {
        return $query->where('status', 'Sent');
    }

    public function scopeForTeacher(Builder $query, $teacherId)
    {
        return $query->where('teacher_id', $teacherId);
    }

    public function scopePendingApproval(Builder $query)
    {
        return $query->where('status', 'Submitted');
    }

    public function calculateTotalScore()
    {
        $total = $this->reportScores()->sum('score');

        $this->update([
            'total_score' => $total
        ]);

        return $total;
    }

    public function isComplete()
    {
        return $this->reportScores()->count() > 0;
    }
}
