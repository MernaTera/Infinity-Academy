<?php

namespace App\Models\Finance;

use Illuminate\Database\Eloquent\Model;
use App\Models\Academic\CourseTemplate;
use App\Models\HR\Employee;

class LevelPackage extends Model
{
    protected $table      = 'level_package';
    protected $primaryKey = 'package_id';
    public $timestamps    = true;

    protected $casts = [
        'levels_count'        => 'integer',
        'package_price'       => 'decimal:2',
        'is_active'           => 'boolean',
        'created_by_admin_id' => 'integer',
    ];

    protected $fillable = [
        'course_template_id',
        'name',
        'levels_count',
        'package_price',
        'is_active',
        'created_by_admin_id',
    ];

    // ─────────────────────────────────────────
    // Relations
    // ─────────────────────────────────────────

    public function courseTemplate()
    {
        return $this->belongsTo(CourseTemplate::class, 'course_template_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(Employee::class, 'created_by_admin_id');
    }

    // ─────────────────────────────────────────
    // Helpers
    // ─────────────────────────────────────────

    /**
     * Price per level within this package
     */
    public function pricePerLevel(): float
    {
        if ($this->levels_count <= 0) return 0;
        return round($this->package_price / $this->levels_count, 2);
    }

    /**
     * Savings compared to buying levels individually
     */
    public function savingsVsIndividual(float $individualLevelPrice): float
    {
        $fullPrice = $individualLevelPrice * $this->levels_count;
        return max(0, $fullPrice - $this->package_price);
    }

    // ─────────────────────────────────────────
    // Scopes
    // ─────────────────────────────────────────

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeForCourse($query, int $courseId)
    {
        return $query->where('course_template_id', $courseId);
    }
}