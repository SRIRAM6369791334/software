<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Dealer extends Model
{
    use SoftDeletes;

    protected $fillable = ['firm_name', 'gst_number', 'location', 'contact_person', 'phone', 'route', 'pending_amount'];

    protected $casts = ['pending_amount' => 'decimal:2'];

    public function payments(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(DealerPayment::class);
    }

    public function purchases(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Purchase::class, 'vendor_name', 'firm_name');
    }

    public function scopeSearch($query, ?string $term)
    {
        if (!$term) return $query;
        return $query->where(function ($q) use ($term) {
            $q->where('firm_name', 'like', "%{$term}%")
              ->orWhere('contact_person', 'like', "%{$term}%")
              ->orWhere('phone', 'like', "%{$term}%");
        });
    }

    public function getFormattedPendingAttribute(): string
    {
        return $this->pending_amount > 0 ? '₹' . number_format($this->pending_amount, 0, '.', ',') : '—';
    }
}
