<?php

namespace App\Models\Enrollment;

use Illuminate\Database\Eloquent\Model;
use App\Models\HR\Employee;
use App\Models\Enrollment\Enrollment;

/**
 * Class EnrollmentNote
 *
 * A free-text note attached to an enrollment (written by CS / Student Care).
 * Notes follow the student across a postponement → resume: when a postponed
 * enrollment is resumed into a new enrollment of the SAME course, its notes
 * are copied over so nothing the team wrote is lost.
 *
 * @property int         $note_id
 * @property int         $enrollment_id
 * @property string      $note
 * @property int|null    $created_by_employee_id
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 *
 * @package App\Models\Enrollment
 */
class EnrollmentNote extends Model
{
    protected $table = 'enrollment_note';
    protected $primaryKey = 'note_id';
    public $timestamps = true;

    protected $casts = [
        'enrollment_id'          => 'integer',
        'created_by_employee_id' => 'integer',
        'created_at'             => 'datetime',
        'updated_at'             => 'datetime',
    ];

    protected $fillable = [
        'enrollment_id',
        'note',
        'created_by_employee_id',
    ];

    public function enrollment()
    {
        return $this->belongsTo(Enrollment::class, 'enrollment_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(Employee::class, 'created_by_employee_id');
    }
}
