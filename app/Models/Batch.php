<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Batch extends Model
{
    protected $fillable = [
        'batch_code',
        'placement_date',
        'initial_count',
        'current_count',
        'breed',
        'avg_placement_weight',
        'status',
        'closed_at'
    ];

    protected $casts = [
        'placement_date' => 'date',
        'closed_at' => 'date',
    ];

    public function scopeActive($query)
    {
        return $query->where('status', 'Active');
    }
}
