<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Dealer extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'firm_name', 'gst_number', 'location', 'contact_person', 'phone', 'route', 'route_id', 'pending_amount'
    ];

    protected $casts = [
        'pending_amount' => 'decimal:2'
    ];

    // ── Relationships ──────────────────────────────────────────────────────────
    public function routeRelation()
    {
        return $this->belongsTo(Route::class, 'route_id');
    }

    public function payments(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(DealerPayment::class);
    }

    public function dayLoadEntries(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(DayLoadEntry::class);
    }

    public function purchases(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Purchase::class, 'vendor_name', 'firm_name');
    }

    public function dealerPurchases(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(DealerPurchase::class);
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

    /**
     * Day-load outstanding: sum of all non-cancelled entry amounts
     * minus payments made against those entries.
     *
     * Business rationale: liability accrues at delivery, not at invoicing.
     * A dealer owes for birds received even if the batch hasn't been
     * finalized into an invoice. Entry amounts (bird_weight × customer_rate)
     * are fixed at recording time with no later adjustment, so counting
     * un-invoiced entries gives an accurate view of true economic liability.
     */
    public function getDayloadOutstandingAttribute(): float
    {
        $entries = $this->dayLoadEntries()
            ->where('status', '!=', 'Cancelled')
            ->with(['dealerPayments' => fn($q) => $q->where('dealer_id', $this->id)])
            ->get();

        $outstanding = $entries->sum(function ($entry) {
            return (float) $entry->amount - (float) $entry->dealerPayments->sum('amount');
        });

        return max(0, $outstanding);
    }

    /**
     * Merged outstanding displayed in dealer detail views:
     * old-system pending_amount + day-load outstanding.
     */
    public function getDisplayedOutstandingAttribute(): float
    {
        return (float) $this->pending_amount + $this->dayload_outstanding;
    }

    protected static function booted()
    {
        static::created(function ($dealer) {
            $users = \App\Models\User::all();
            \Illuminate\Support\Facades\Notification::send($users, new \App\Notifications\ActivityNotification(
                'New Dealer Added',
                "Dealer {$dealer->firm_name} was registered.",
                route('masters.dealers.show', $dealer->id),
                'storefront',
                'blue'
            ));
        });
    }
}
