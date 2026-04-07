<?php

namespace App\Models\Enrollment;

use Illuminate\Database\Eloquent\Model;

class Material extends Model
{
    protected $table = 'materials';
    protected $primaryKey = 'material_id';
    protected $fillable = ['name', 'price', 'is_active'];

    public function assignments()
    {
        return $this->hasMany(MaterialAssignment::class);
    }
}
