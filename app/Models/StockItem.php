<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockItem extends Model
{
    protected $table = 'stock_items';

    protected $fillable = ['item_name', 'category', 'unit', 'current_stock', 'reorder_level'];

    public function transactions()
    {
        return $this->hasMany(StockTransaction::class, 'item_name', 'item_name');
    }
}
