<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DayLoadBatch extends Model
{
    use HasFactory;

    protected $fillable = [
        'billing_date',
        'status',
        'total_boxes',
        'total_box_weight',
        'total_empty_weight',
        'total_bird_weight',
        'total_farm_weight',
        'total_loss_weight',
        'invoice_id',
    ];

    protected $casts = [
        'billing_date' => 'date',
        'total_boxes' => 'decimal:2',
        'total_box_weight' => 'decimal:2',
        'total_empty_weight' => 'decimal:2',
        'total_bird_weight' => 'decimal:2',
        'total_farm_weight' => 'decimal:2',
        'total_loss_weight' => 'decimal:2',
    ];

    public function entries(): HasMany
    {
        return $this->hasMany(DayLoadEntry::class, 'batch_id');
    }

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(DayLoadInvoice::class, 'invoice_id');
    }
}
