<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DealerPayment extends Model
{
    protected $fillable = ['dealer_id', 'date', 'amount', 'payment_mode', 'notes'];

    protected $casts = [
        'date'   => 'date',
        'amount' => 'decimal:2',
    ];

    public function dealer(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Dealer::class);
    }

    public function scopeSearch($query, ?string $term)
    {
        if (!$term) return $query;
        return $query->whereHas('dealer', fn($q) => $q->where('firm_name', 'like', "%{$term}%"));
    }
}
