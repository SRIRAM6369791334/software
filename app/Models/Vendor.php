<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Vendor extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = ['firm_name', 'is_shop', 'gst_number', 'location', 'contact_person', 'phone', 'route', 'notes'];

    protected $casts = [
        'is_shop' => 'boolean',
    ];

    public function dayLoadEntries(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(DayLoadEntry::class);
    }

    public function purchases(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Purchase::class);
    }

    public function vendorPayments(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(VendorPayment::class);
    }

    public function getOutstandingBalanceAttribute(): float
    {
        $totalCreditPurchases = $this->relationLoaded('purchases') 
            ? (float) $this->purchases->where('payment_mode', 'Credit')->sum('total_amount')
            : (float) $this->purchases()->where('payment_mode', 'Credit')->sum('total_amount');

        $dayLoadEntries = $this->relationLoaded('dayLoadEntries')
            ? $this->dayLoadEntries->where('status', '!=', 'Cancelled')
            : $this->dayLoadEntries()->where('status', '!=', 'Cancelled')->get();

        $totalDayLoadLiabilities = (float) $dayLoadEntries->sum(function ($entry) {
            return $entry->vendor_cost;
        });

        $totalPaymentsPaid = $this->relationLoaded('vendorPayments')
            ? (float) $this->vendorPayments->sum('amount')
            : (float) $this->vendorPayments()->sum('amount');
        
        return round(($totalCreditPurchases + $totalDayLoadLiabilities) - $totalPaymentsPaid, 2);
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

    public function scopeShops($query)
    {
        return $query->where('is_shop', true);
    }

    protected static function booted()
    {
        static::created(function ($vendor) {
            $users = \App\Models\User::all();
            \Illuminate\Support\Facades\Notification::send($users, new \App\Notifications\ActivityNotification(
                'New Vendor Added',
                "Vendor {$vendor->firm_name} was registered.",
                route('masters.vendors.show', $vendor->id),
                'local_shipping',
                'amber'
            ));
        });
    }
}
