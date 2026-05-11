<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BirdBatch extends Model
{
    protected $fillable = [
        'batch_name', 'date_received', 'initial_count', 'current_count', 'avg_weight'
    ];

    protected $casts = [
        'date_received' => 'date',
    ];
}
