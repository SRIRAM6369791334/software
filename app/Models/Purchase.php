<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

use Illuminate\Database\Eloquent\Model;

class Purchase extends Model
{
    use HasFactory;
    protected $fillable = [
        'vendor_id', 'vendor_name', 'date', 'item', 'quantity', 'unit',
        'rate', 'gst_percentage', 'gst_amount', 'total_amount', 'payment_mode',
    ];

    public function vendor(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Vendor::class);
    }

    protected $casts = [
        'date'           => 'date',
        'quantity'       => 'decimal:2',
        'rate'           => 'decimal:2',
        'gst_percentage' => 'decimal:2',
        'gst_amount'     => 'decimal:2',
        'total_amount'   => 'decimal:2',
    ];

    public function scopeSearch($query, ?string $term)
    {
        if (!$term) return $query;
        return $query->where(function ($q) use ($term) {
            $q->where('vendor_name', 'like', "%{$term}%")
              ->orWhere('item', 'like', "%{$term}%");
        });
    }

    /**
     * Auto-compute GST amount and total before saving.
     */
    public static function boot(): void
    {
        parent::boot();
        static::saving(function (self $model) {
            $base = $model->quantity * $model->rate;
            $model->gst_amount  = round($base * $model->gst_percentage / 100, 2);
            $model->total_amount = $base + $model->gst_amount;
        });
    }
}
