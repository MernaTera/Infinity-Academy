<?php

namespace App\Models\Enrollment;

use Illuminate\Database\Eloquent\Model;

class MaterialAssignment extends Model
{
    protected $table = 'material_assignment';
    protected $fillable = [
        'material_id',
        'course_template_id',
        'level_id',
        'sublevel_id',
        'is_mandatory'
    ];

    public function material()
    {
        return $this->belongsTo(Material::class);
    }
}
