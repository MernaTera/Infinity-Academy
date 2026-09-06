<?php

namespace App\Models\Enrollment;

use Illuminate\Database\Eloquent\Model;
use App\Models\HR\Employee;

class EnrollmentNote extends Model
{
    protected $table = 'enrollment_note';
    protected $primaryKey = 'note_id';

    protected $fillable = [
        'enrollment_id',
        'created_by_employee_id',
        'note',
    ];

    public function enrollment()
    {
        return $this->belongsTo(Enrollment::class, 'enrollment_id', 'enrollment_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(Employee::class, 'created_by_employee_id', 'employee_id');
    }
}
