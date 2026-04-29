<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockSummary extends Model
{
    protected $table = 'stock_summary';
    
    protected $fillable = [
        'item_name', 'unit', 'current_stock', 'last_updated'
    ];

    protected $casts = [
        'current_stock' => 'decimal:3',
        'last_updated' => 'datetime',
    ];
}
