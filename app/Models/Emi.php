<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Emi extends Model
{
    use HasFactory;
    protected $table = 'emis';

    protected $fillable = ['loan_name', 'bank_name', 'amount', 'paid_amount', 'due_date', 'status', 'emi_type', 'entity_id'];

    protected $casts = [
        'due_date'    => 'date',
        'amount'      => 'decimal:2',
        'paid_amount' => 'decimal:2',
    ];

    protected $appends = ['remaining_amount'];

    public function getRemainingAmountAttribute()
    {
        return max(0, $this->amount - $this->paid_amount);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'entity_id');
    }

    public function dealer()
    {
        return $this->belongsTo(Dealer::class, 'entity_id');
    }

    public function vendor()
    {
        return $this->belongsTo(Vendor::class, 'entity_id');
    }
}
