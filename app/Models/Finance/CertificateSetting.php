<?php

namespace App\Models\Finance;

use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\BelongsToBranch;

class CertificateSetting extends Model
{
    use BelongsToBranch;

    protected $table    = 'certificate_settings';
    protected $fillable = ['name', 'fee', 'is_active', 'branch_id'];
    protected $casts    = ['fee' => 'decimal:2', 'is_active' => 'boolean'];

    public function scopeActive($q) { return $q->where('is_active', true); }

    public function serviceSales()
    {
        return $this->hasMany(ServiceSale::class, 'certificate_setting_id');
    }
}
