<?php

namespace App\Models\Enrollment;

use Illuminate\Database\Eloquent\Model;

class Material extends Model
{
    protected $table = 'materials';
    protected $primaryKey = 'material_id';

    protected $fillable = [
        'name',
        'price',
        'revenue_type',
        'is_active',
        'created_by_admin_id',
    ];

    protected $casts = [
        'price'     => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function assignments()
    {
        return $this->hasMany(MaterialAssignment::class);
    }
}