<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class StockMovement extends Model
{
    protected $fillable = [
        'type', 'item_name', 'quantity', 'unit', 'rate', 
        'reference_type', 'reference_id', 'notes', 'date', 'created_by'
    ];

    protected $casts = [
        'date' => 'date',
        'quantity' => 'decimal:3',
        'rate' => 'decimal:2',
    ];

    public function reference(): MorphTo
    {
        return $this->morphTo();
    }
}
