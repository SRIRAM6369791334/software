<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DealerPurchaseItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'dealer_purchase_id',
        'item_name',
        'quantity_kg',
        'rate_per_kg',
        'tax_amount',
        'total_amount'
    ];

    protected $casts = [
        'quantity_kg'  => 'decimal:2',
        'rate_per_kg'  => 'decimal:2',
        'tax_amount'   => 'decimal:2',
        'total_amount' => 'decimal:2',
    ];

    public function dealerPurchase(): BelongsTo
    {
        return $this->belongsTo(DealerPurchase::class);
    }
}
