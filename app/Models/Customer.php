<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customer extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = ['name', 'phone', 'address', 'gst_number', 'route', 'type', 'balance'];

    protected $casts = ['balance' => 'decimal:2'];

    // ── Relationships ──────────────────────────────────────────────────────────
    public function weeklyBills(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(WeeklyBill::class);
    }

    public function dailyBills(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(DailyBill::class);
    }

    public function payments(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(CustomerPayment::class);
    }

    // ── Scopes ─────────────────────────────────────────────────────────────────
    public function scopeSearch($query, ?string $term)
    {
        if (!$term) return $query;
        return $query->where(function ($q) use ($term) {
            $q->where('name', 'like', "%{$term}%")
              ->orWhere('phone', 'like', "%{$term}%")
              ->orWhere('route', 'like', "%{$term}%");
        });
    }

    public function scopeWithBalance($query)
    {
        return $query->where('balance', '>', 0);
    }

    // ── Accessors ──────────────────────────────────────────────────────────────
    public function getFormattedBalanceAttribute(): string
    {
        return $this->balance > 0 ? '₹' . number_format($this->balance, 0, '.', ',') : '—';
    }
}
