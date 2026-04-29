<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WeeklyBill extends Model
{
    protected $fillable = ['customer_id', 'period_start', 'period_end', 'items_description', 'quantity_kg', 'amount', 'status'];

    protected $casts = [
        'period_start' => 'date',
        'period_end'   => 'date',
        'amount'       => 'decimal:2',
        'quantity_kg'  => 'decimal:2',
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
        return 'INV-W-' . str_pad($this->id, 4, '0', STR_PAD_LEFT);
    }
}
