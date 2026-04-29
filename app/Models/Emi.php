<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

use Illuminate\Database\Eloquent\Model;

class Emi extends Model
{
    use HasFactory;
    protected $table = 'emis';

    protected $fillable = ['item', 'amount', 'due_date', 'status'];

    protected $casts = [
        'due_date' => 'date',
        'amount'   => 'decimal:2',
    ];
}
