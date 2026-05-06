<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Mortality extends Model
{
    protected $table = 'poultry_mortalities';

    protected $fillable = [
        'date',
        'batch_id',
        'count',
        'reason',
        'remarks',
        'created_by'
    ];

    protected $casts = [
        'date' => 'date',
        'count' => 'integer'
    ];

    public function batch(): BelongsTo
    {
        return $this->belongsTo(Batch::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
