<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DailyBill extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id', 'date', 'amount', 'gst_percentage', 'gst_amount', 
        'net_amount', 'payment_mode', 'status'
    ];

    protected $casts = [
        'date'       => 'date',
        'amount'     => 'decimal:2',
        'gst_amount' => 'decimal:2',
        'net_amount' => 'decimal:2',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(DailyBillItem::class);
    }

    public function scopeSearch($query, ?string $term)
    {
        if (!$term) return $query;
        return $query->whereHas('customer', fn($q) => $q->where('name', 'like', "%{$term}%"))
                     ->orWhereHas('items', fn($q) => $q->where('item_name', 'like', "%{$term}%"));
    }

    public function getInvoiceNumberAttribute(): string
    {
        return 'INV-D-' . str_pad($this->id, 4, '0', STR_PAD_LEFT);
    }

    /*
    |--------------------------------------------------------------------------
    | Backward Compatibility Accessors
    |--------------------------------------------------------------------------
    */

    public function getItemsDescriptionAttribute(): ?string
    {
        if (array_key_exists('items_description', $this->attributes)) {
            return $this->attributes['items_description'];
        }
        $firstItem = $this->items()->first();
        return $firstItem ? $firstItem->item_name : null;
    }

    public function getQuantityKgAttribute(): float
    {
        if (array_key_exists('quantity_kg', $this->attributes)) {
            return (float) $this->attributes['quantity_kg'];
        }
        return (float) $this->items()->sum('quantity_kg');
    }

    public function getRatePerKgAttribute(): float
    {
        if (array_key_exists('rate_per_kg', $this->attributes)) {
            return (float) $this->attributes['rate_per_kg'];
        }
        $firstItem = $this->items()->first();
        return $firstItem ? (float) $firstItem->rate_per_kg : 0.0;
    }
}

