<?php

namespace App\Models\Finance;

use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\BelongsToBranch;
use App\Models\Academic\Patch;
use App\Models\Core\Branch;
use App\Models\HR\Employee;
use App\Models\Leads\Lead;
use App\Models\Student\Student;

class ServiceSale extends Model
{
    use BelongsToBranch;

    protected $table      = 'service_sale';
    protected $primaryKey = 'service_sale_id';

    protected $fillable = [
        'branch_id',
        'patch_id',
        'lead_id',
        'student_id',
        'client_name',
        'client_phone',
        'service_type',
        'test_fee_setting_id',
        'certificate_setting_id',
        'material_id',
        'enrollment_id',
        'item_name',
        'price',
        'payment_method',
        'reference_number',
        'notes',
        'created_by_cs_id',
    ];

    protected $casts = [
        'branch_id'              => 'integer',
        'patch_id'               => 'integer',
        'lead_id'                => 'integer',
        'student_id'             => 'integer',
        'test_fee_setting_id'    => 'integer',
        'certificate_setting_id' => 'integer',
        'material_id'            => 'integer',
        'enrollment_id'          => 'integer',
        'price'                  => 'decimal:2',
        'created_by_cs_id'       => 'integer',
        'created_at'             => 'datetime',
        'updated_at'             => 'datetime',
    ];

    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }

    public function patch()
    {
        return $this->belongsTo(Patch::class, 'patch_id');
    }

    public function lead()
    {
        return $this->belongsTo(Lead::class, 'lead_id');
    }

    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id');
    }

    public function testFeeSetting()
    {
        return $this->belongsTo(TestFeeSetting::class, 'test_fee_setting_id');
    }

    public function material()
    {
        return $this->belongsTo(\App\Models\Enrollment\Material::class, 'material_id');
    }

    public function enrollment()
    {
        return $this->belongsTo(\App\Models\Enrollment\Enrollment::class, 'enrollment_id');
    }

    public function certificateSetting()
    {
        return $this->belongsTo(CertificateSetting::class, 'certificate_setting_id');
    }

    public function createdByCs()
    {
        return $this->belongsTo(Employee::class, 'created_by_cs_id');
    }

    public function financialTransaction()
    {
        return $this->hasOne(FinancialTransaction::class, 'service_sale_id');
    }

    public function isTest(): bool
    {
        return $this->service_type === 'Test';
    }

    public function isCertificate(): bool
    {
        return $this->service_type === 'Certificate';
    }

    public function isMaterial(): bool
    {
        return $this->service_type === 'Material';
    }

    public function getReceiptNumberAttribute(): string
    {
        return 'SRV-' . str_pad((string) $this->service_sale_id, 6, '0', STR_PAD_LEFT);
    }

    public function getPaymentMethodLabelAttribute(): string
    {
        return match ($this->payment_method) {
            'Transfer' => 'Instapay',
            'Online'   => 'Vodafone Cash',
            default    => (string) $this->payment_method,
        };
    }
}
