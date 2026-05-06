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
        'vendor_id', 'vendor_name', 'date', 'gst_percentage', 'gst_amount', 'total_amount', 'payment_mode',
    ];

    protected $casts = [
        'date'           => 'date',
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
}
