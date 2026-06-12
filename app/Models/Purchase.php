<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Purchase extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'vendor_id', 'vendor_name', 'invoice_no', 'date', 'due_date', 'gst_percentage', 'gst_amount', 'total_amount', 'payment_mode',
    ];

    protected $casts = [
        'date'           => 'date',
        'due_date'       => 'date',
        'gst_percentage' => 'decimal:2',
        'gst_amount'     => 'decimal:2',
        'total_amount'   => 'decimal:2',
    ];

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(PurchaseItem::class);
    }

    public function scopeSearch($query, ?string $term)
    {
        if (!$term) return $query;
        return $query->where(function ($q) use ($term) {
            $q->where('vendor_name', 'like', "%{$term}%")
              ->orWhereHas('items', function($iq) use ($term) {
                  $iq->where('item_name', 'like', "%{$term}%");
              });
        });
    }

    /*
    |--------------------------------------------------------------------------
    | Backward Compatibility Accessors
    |--------------------------------------------------------------------------
    */

    public function getQuantityAttribute(): float
    {
        if (array_key_exists('quantity', $this->attributes)) {
            return (float) $this->attributes['quantity'];
        }
        return (float) $this->items()->sum('quantity');
    }

    public function getUnitAttribute(): ?string
    {
        if (array_key_exists('unit', $this->attributes)) {
            return $this->attributes['unit'];
        }
        $firstItem = $this->items()->first();
        return $firstItem ? $firstItem->unit : null;
    }

    public function getRateAttribute(): float
    {
        if (array_key_exists('rate', $this->attributes)) {
            return (float) $this->attributes['rate'];
        }
        $firstItem = $this->items()->first();
        return $firstItem ? (float) $firstItem->rate : 0.0;
    }

    public function getItemAttribute(): ?string
    {
        if (array_key_exists('item', $this->attributes)) {
            return $this->attributes['item'];
        }
        $firstItem = $this->items()->first();
        return $firstItem ? $firstItem->item_name : null;
    }
}

