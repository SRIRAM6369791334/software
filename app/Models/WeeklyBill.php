<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WeeklyBill extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'customer_id', 'period_start', 'period_end', 'invoice_no',
        'amount', 'gst_percentage', 'gst_amount', 'net_amount', 
        'status', 'payment_mode'
    ];

    protected $casts = [
        'period_start' => 'date',
        'period_end'   => 'date',
        'amount'       => 'decimal:2',
        'gst_amount'   => 'decimal:2',
        'net_amount'   => 'decimal:2',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(WeeklyBillItem::class);
    }

    public function scopeSearch($query, ?string $term)
    {
        if (!$term) return $query;
        return $query->whereHas('customer', fn($q) => $q->where('name', 'like', "%{$term}%"))
                     ->orWhereHas('items', fn($q) => $q->where('item_name', 'like', "%{$term}%"));
    }

    public function getInvoiceNumberAttribute(): string
    {
        return 'INV-W-' . str_pad($this->id, 4, '0', STR_PAD_LEFT);
    }

    /*
    |--------------------------------------------------------------------------
    | Backward Compatibility Accessors
    |--------------------------------------------------------------------------
    */

    public function getItemsDescriptionAttribute(): ?string
    {
        if (array_key_exists('items_description', $this->attributes) && $this->attributes['items_description'] !== null) {
            return $this->attributes['items_description'];
        }
        return $this->items->pluck('item_name')->implode(', ');
    }

    public function getQuantityKgAttribute(): float
    {
        if (array_key_exists('quantity_kg', $this->attributes) && $this->attributes['quantity_kg'] !== null) {
            return (float) $this->attributes['quantity_kg'];
        }
        return (float) $this->items->sum('quantity_kg');
    }
}
