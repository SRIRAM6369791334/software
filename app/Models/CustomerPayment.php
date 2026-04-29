<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

use Illuminate\Database\Eloquent\Model;

class CustomerPayment extends Model
{
    use HasFactory;
    protected $fillable = ['customer_id', 'date', 'amount', 'payment_mode', 'payment_type', 'balance_after', 'notes'];

    protected $casts = [
        'date'         => 'date',
        'amount'       => 'decimal:2',
        'balance_after'=> 'decimal:2',
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
}

