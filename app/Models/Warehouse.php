<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Warehouse extends Model
{
    protected $fillable = [
        'name',
        'location',
        'is_active'
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
