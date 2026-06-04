<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles, HasApiTokens;

    protected $fillable = ['name', 'email', 'password', 'phone', 'username', 'is_active'];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    public function activityLogs()
    {
        return $this->hasMany(ActivityLog::class);
    }

    /**
     * Get the highest role level for the user.
     */
    public function getRoleLevel(): int
    {
        $hierarchy = [
            'viewer' => 1,
            'staff' => 2,
            'delivery_staff' => 2,
            'data_entry' => 2,
            'accountant' => 3,
            'manager' => 3,
            'admin' => 4
        ];

        return $this->roles->map(fn($role) => $hierarchy[$role->name] ?? 0)->max() ?? 0;
    }
}
