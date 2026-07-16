<?php

namespace App\Services;

use App\Models\CashBankLedger;
use App\Models\DealerPayment;
use App\Models\Expense;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class CashBankLedgerService
{
    public function getOrCreateForDate(Carbon $date): CashBankLedger
    {
        return DB::transaction(function () use ($date) {
            $ledger = CashBankLedger::whereDate('ledger_date', $date)->first();

            if ($ledger) {
                return $ledger;
            }

            $previous = CashBankLedger::whereDate('ledger_date', '<', $date)
                ->orderBy('ledger_date', 'desc')
                ->first();

            if ($previous) {
                // BUG 2 FIX: Always carry the actual closing balance.
                // After approval, closing_cash_balance is already 0 (swept to bank),
                // so we don't need a special case for is_approved.
                $openingCash = (float) $previous->closing_cash_balance;
                $openingBank = (float) $previous->closing_bank_balance;
            } else {
                $openingCash = 0;
                $openingBank = 0;
            }

            return CashBankLedger::create([
                'ledger_date'           => $date,
                'opening_cash_balance'  => $openingCash,
                'opening_bank_balance'  => $openingBank,
                'cash_income'           => 0,
                'bank_income'           => 0,
                'cash_expense'          => 0,
                'bank_expense'          => 0,
                'closing_cash_balance'  => $openingCash,
                'closing_bank_balance'  => $openingBank,
                'is_approved'           => false,
            ]);
        });
    }

    public function recalculateForDate(Carbon $date): CashBankLedger
    {
        return DB::transaction(function () use ($date) {
            $ledger = $this->getOrCreateForDate($date);

            $dateStr = $date->format('Y-m-d');

            // Cash Income = Dealer payments + Customer COD payments
            $dealerCashIncome   = (float) DealerPayment::whereDate('date', $dateStr)->sum('cash_amount');
            $customerCashIncome = (float) \App\Models\CustomerPayment::whereDate('date', $dateStr)->sum('cod_amount');
            $cashIncome         = round($dealerCashIncome + $customerCashIncome, 2);

            // Bank Income = Dealer bank payments + Customer bank transfer payments
            $dealerBankIncome   = (float) DealerPayment::whereDate('date', $dateStr)->sum('bank_amount');
            $customerBankIncome = (float) \App\Models\CustomerPayment::whereDate('date', $dateStr)->sum('bank_transfer_amount');
            $bankIncome         = round($dealerBankIncome + $customerBankIncome, 2);

            // Cash Expense = Cash Expenses + Vendor Cash Payments
            $expenseCash = (float) Expense::whereDate('date', $dateStr)->where('payment_method', 'Cash')->sum('amount');
            $vendorCash  = (float) \App\Models\VendorPayment::whereDate('date', $dateStr)->sum('cash_amount');
            $cashExpense = round($expenseCash + $vendorCash, 2);

            // BUG 1 FIX: Bank Expense = Bank Transfer Expenses + Vendor Bank Payments — now saved to DB
            $expenseBank = (float) Expense::whereDate('date', $dateStr)->where('payment_method', 'Bank Transfer')->sum('amount');
            $vendorBank  = (float) \App\Models\VendorPayment::whereDate('date', $dateStr)->sum('bank_amount');
            $bankExpense = round($expenseBank + $vendorBank, 2);

            $ledger->update([
                'cash_income'  => $cashIncome,
                'bank_income'  => $bankIncome,
                'cash_expense' => $cashExpense,
                'bank_expense' => $bankExpense, // BUG 1 FIX: persist bank_expense
            ]);

            // Only update closing balances if the day is NOT approved
            // (approved days have their closing balances frozen by approve())
            if (!$ledger->is_approved) {
                $closingCash = round(
                    (float) $ledger->opening_cash_balance + $cashIncome - $cashExpense,
                    2
                );
                $closingBank = round(
                    (float) $ledger->opening_bank_balance + $bankIncome - $bankExpense,
                    2
                );

                $ledger->updateQuietly([
                    'closing_cash_balance' => $closingCash,
                    'closing_bank_balance' => $closingBank,
                ]);

                // BUG 5 FIX: Cascade-update the next day's opening balances
                // so all subsequent days reflect the correct running balance.
                $this->cascadeNextDayOpening($date, $closingCash, $closingBank);
            }

            return $ledger->fresh();
        });
    }

    /**
     * BUG 5 FIX: After recalculating a day, update the immediately next ledger row's
     * opening balances (if it is not yet approved). This keeps the running balance chain intact.
     */
    private function cascadeNextDayOpening(Carbon $date, float $closingCash, float $closingBank): void
    {
        $nextDay = CashBankLedger::whereDate('ledger_date', $date->copy()->addDay())->first();

        if (!$nextDay || $nextDay->is_approved) {
            return;
        }

        // Only update if values actually changed to avoid unnecessary DB writes
        if (
            (float) $nextDay->opening_cash_balance !== $closingCash ||
            (float) $nextDay->opening_bank_balance !== $closingBank
        ) {
            $nextDay->updateQuietly([
                'opening_cash_balance' => $closingCash,
                'opening_bank_balance' => $closingBank,
                // Recalculate closing for the next day too
                'closing_cash_balance' => round($closingCash + (float) $nextDay->cash_income - (float) $nextDay->cash_expense, 2),
                'closing_bank_balance' => round($closingBank + (float) $nextDay->bank_income - (float) $nextDay->bank_expense, 2),
            ]);

            // Recurse one level deeper (cascade forward)
            $newClosingCash = round($closingCash + (float) $nextDay->cash_income - (float) $nextDay->cash_expense, 2);
            $newClosingBank = round($closingBank + (float) $nextDay->bank_income - (float) $nextDay->bank_expense, 2);
            $this->cascadeNextDayOpening(
                Carbon::parse($nextDay->ledger_date),
                $newClosingCash,
                $newClosingBank
            );
        }
    }

    /**
     * Approve a day's ledger, sweeping closing_cash_balance into the bank side.
     *
     * TODO: restrict to Admin role once role-based permissions are finalized.
     *
     * @throws \RuntimeException if the ledger is already approved
     */
    public function approve(CashBankLedger $ledger, int $approvedByUserId): CashBankLedger
    {
        if ($ledger->is_approved) {
            throw new \RuntimeException(
                "Ledger for {$ledger->ledger_date->format('Y-m-d')} is already approved."
            );
        }

        return DB::transaction(function () use ($ledger, $approvedByUserId) {
            $sweepAmount = (float) $ledger->closing_cash_balance;

            $newClosingBank = round(
                (float) $ledger->closing_bank_balance + $sweepAmount,
                2
            );

            $ledger->update([
                'is_approved'          => true,
                'approved_amount'      => $sweepAmount,
                'approved_by'          => $approvedByUserId,
                'approved_at'          => now(),
                'closing_cash_balance' => 0,
                'closing_bank_balance' => $newClosingBank,
            ]);

            // Cascade so the next day correctly starts with cash=0, bank=newClosingBank
            $this->cascadeNextDayOpening(
                Carbon::parse($ledger->ledger_date),
                0,
                $newClosingBank
            );

            return $ledger->fresh();
        });
    }
}
