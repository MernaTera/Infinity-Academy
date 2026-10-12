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

    public static function standingFor($teacherId, $patchId): ?self
    {
        if (!$teacherId || !$patchId) return null;

        $targetPatch = \App\Models\Academic\Patch::withoutGlobalScopes()->find($patchId);
        if (!$targetPatch) return null;

        return static::with('contractType')
            ->where('teacher_contract.teacher_id', $teacherId)
            ->where('teacher_contract.is_active', true)
            ->join('patch', 'patch.patch_id', '=', 'teacher_contract.patch_id')
            ->where('patch.start_date', '<=', $targetPatch->start_date)
            ->orderByDesc('patch.start_date')
            ->select('teacher_contract.*')
            ->first();
    }
}