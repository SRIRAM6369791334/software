<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    const ROLE_HIERARCHY = ['viewer' => 1, 'staff' => 2, 'manager' => 3, 'admin' => 4];

    protected $fillable = ['name', 'email', 'password', 'phone'];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // ── Relationships ──────────────────────────────────────────────────────────
    public function roles(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'user_roles')
                    ->withTimestamps()
                    ->withPivot('assigned_by');
    }

    public function userRoles(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(UserRole::class);
    }

    // ── Role helpers ───────────────────────────────────────────────────────────
    public function hasRole(string $role): bool
    {
        return $this->roles()->where('name', $role)->exists();
    }

    public function getRoleLevel(): int
    {
        return $this->roles->map(fn($r) => self::ROLE_HIERARCHY[$r->name] ?? 0)->max() ?? 0;
    }

    public function hasMinRole(string $minRole): bool
    {
        return $this->getRoleLevel() >= (self::ROLE_HIERARCHY[$minRole] ?? 0);
    }

    public function getIsAdminAttribute(): bool   { return $this->hasMinRole('admin'); }
    public function getIsManagerAttribute(): bool { return $this->hasMinRole('manager'); }
    public function getIsStaffAttribute(): bool   { return $this->hasMinRole('staff'); }
    public function getIsViewerAttribute(): bool  { return $this->hasMinRole('viewer'); }

    public function getRoleNamesAttribute(): array
    {
        return $this->roles->pluck('name')->toArray();
    }
}
