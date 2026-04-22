<?php

namespace App\Models\Auth;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Auth\User as Authenticatable;
use App\Models\Auth\Role;
use App\Models\HR\Employee;
use App\Models\Student\Student;

class User extends Authenticatable
{
    protected $table = 'users';

    protected $casts = [
        'email_verified_at' => 'datetime',
        'role_id'           => 'integer',
        'is_active'         => 'boolean',
        'failed_attempts'   => 'integer',
        'locked_until'      => 'datetime',
        'last_login_at'     => 'datetime',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected $fillable = [
        'username', 'name', 'email', 'email_verified_at',
        'password', 'remember_token', 'role_id', 'is_active',
        'failed_attempts', 'locked_until', 'last_login_at',
    ];

    // ─────────────────────────────────────────
    // Relations
    // ─────────────────────────────────────────

    public function role()
    {
        return $this->belongsTo(Role::class, 'role_id');
    }

    /** Single employee profile linked to this user */
    public function employee()
    {
        return $this->hasOne(Employee::class, 'user_id');
    }

    /** Student profile (future phase) */
    public function student()
    {
        return $this->hasOne(Student::class, 'user_id');
    }

    // ─────────────────────────────────────────
    // Role helpers  — match exact DB role_names
    // ─────────────────────────────────────────

    public function hasRole(string $roleName): bool
    {
        return $this->role && $this->role->role_name === $roleName;
    }

    public function isAdmin(): bool    { return $this->hasRole('Admin'); }
    public function isCS(): bool       { return $this->hasRole('Customer Service'); }
    public function isSC(): bool       { return $this->hasRole('Student Care'); }
    public function isTeacher(): bool  { return $this->hasRole('Teacher'); }
    public function isStudent(): bool  { return $this->hasRole('Student'); }

    // ─────────────────────────────────────────
    // Permission check — always fresh load
    // ─────────────────────────────────────────

    /**
     * Check if this user's role has a given permission key.
     * Always ensures role + permissions are loaded.
     */
    public function canDo(string $permissionKey): bool
    {
        // Admin bypass — always allowed
        if ($this->isAdmin()) return true;

        // Ensure role with permissions is loaded
        if (!$this->relationLoaded('role') || !$this->role?->relationLoaded('permissions')) {
            $this->load('role.permissions');
        }

        return $this->role?->permissions
            ->contains('permission_key', $permissionKey) ?? false;
    }

    // ─────────────────────────────────────────
    // Account state helpers
    // ─────────────────────────────────────────

    public function isActive(): bool
    {
        return $this->is_active === true;
    }

    public function isLocked(): bool
    {
        return $this->locked_until && $this->locked_until->isFuture();
    }

    public function recordFailedLogin(): void
    {
        $this->increment('failed_attempts');

        if ($this->fresh()->failed_attempts >= 5) {
            $this->update(['locked_until' => now()->addMinutes(15)]);
        }
    }

    // ─────────────────────────────────────────
    // Scopes
    // ─────────────────────────────────────────

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}