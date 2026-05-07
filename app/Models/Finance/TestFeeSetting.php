<?php

namespace App\Models\Finance;

use Illuminate\Database\Eloquent\Model;

class TestFeeSetting extends Model
{
    protected $table    = 'test_fee_settings';
    protected $fillable = ['name', 'fee', 'is_active'];
    protected $casts    = ['fee' => 'decimal:2', 'is_active' => 'boolean'];

    public function scopeActive($q) { return $q->where('is_active', true); }
}