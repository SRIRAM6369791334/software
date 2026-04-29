<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DailyBill extends Model
{
    protected $fillable = [
        'customer_id', 'date', 'items_description', 'quantity_kg', 
        'rate_per_kg', 'amount', 'gst_percentage', 'gst_amount', 
        'net_amount', 'payment_mode', 'status'
    ];

    protected $casts = [
        'date'        => 'date',
        'amount'      => 'decimal:2',
        'quantity_kg' => 'decimal:2',
        'rate_per_kg' => 'decimal:2',
    ];

    public function customer(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function scopeSearch($query, ?string $term)
    {
        if (!$term) return $query;
        return $query->whereHas('customer', fn($q) => $q->where('name', 'like', "%{$term}%"));
    }

    public function getInvoiceNumberAttribute(): string
    {
        return 'INV-D-' . str_pad($this->id, 4, '0', STR_PAD_LEFT);
    }
}
