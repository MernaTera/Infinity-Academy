<?php

namespace App\Models\HR;
use Illuminate\Database\Eloquent\Model;

class TeacherContract extends Model
{
    protected $table      = 'teacher_contract';
    protected $primaryKey = 'contract_id';
    protected $fillable   = ['teacher_id','patch_id','contract_type_id','is_active','created_by_admin_id'];

    public function contractType() { return $this->belongsTo(ContractType::class, 'contract_type_id', 'contract_type_id'); }
    public function teacher()      { return $this->belongsTo(Teacher::class, 'teacher_id', 'teacher_id'); }
    public function patch()        { return $this->belongsTo(\App\Models\Academic\Patch::class, 'patch_id', 'patch_id'); }
}