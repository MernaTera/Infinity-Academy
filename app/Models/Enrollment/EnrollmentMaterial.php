<?php

namespace App\Models\Enrollment;

use Illuminate\Database\Eloquent\Model;

class EnrollmentMaterial extends Model
{
    protected $table = 'enrollment_material';
    protected $fillable = [
        'enrollment_id',
        'material_id',
        'price',
        'status'
    ];
}
