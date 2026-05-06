<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    protected $fillable = [
        'name',
        'code',
        'type',
        'category',
        'brand',
        'base_unit',
        'conversion_rate',
        'is_active'
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function stockLedgers()
    {
        return $this->hasMany(StockLedger::class);
    }

    /**
     * Calculate current stock by summing IN and subtracting OUT
     */
    public function getCurrentStockAttribute()
    {
        $in = $this->stockLedgers()->where('type', 'IN')->sum('quantity');
        $out = $this->stockLedgers()->where('type', 'OUT')->sum('quantity');
        return $in - $out;
    }
}
