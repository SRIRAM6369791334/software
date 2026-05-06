<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DailyBillItem extends Model
{
    protected $fillable = [
        'daily_bill_id',
        'item_name',
        'quantity_kg',
        'rate_per_kg',
        'tax_amount',
        'total_amount'
    ];

    public function dailyBill(): BelongsTo
    {
        return $this->belongsTo(DailyBill::class);
    }
}
