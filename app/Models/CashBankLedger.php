<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CashBankLedger extends Model
{
    use HasFactory;

    protected $fillable = [
        'ledger_date',
        'opening_cash_balance',
        'opening_bank_balance',
        'cash_income',
        'bank_income',
        'cash_expense',
        'closing_cash_balance',
        'closing_bank_balance',
        'is_approved',
        'approved_amount',
        'approved_by',
        'approved_at',
    ];

    protected $casts = [
        'ledger_date'           => 'date',
        'opening_cash_balance'  => 'decimal:2',
        'opening_bank_balance'  => 'decimal:2',
        'cash_income'           => 'decimal:2',
        'bank_income'           => 'decimal:2',
        'cash_expense'          => 'decimal:2',
        'closing_cash_balance'  => 'decimal:2',
        'closing_bank_balance'  => 'decimal:2',
        'is_approved'           => 'boolean',
        'approved_amount'       => 'decimal:2',
        'approved_at'           => 'datetime',
    ];

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}
