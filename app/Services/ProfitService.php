<?php

namespace App\Services;

use App\Models\WeeklyBill;
use App\Models\DailyBill;
use App\Models\Purchase;
use App\Models\Expense;
use App\Models\Emi;
use App\Models\CustomerPayment;
use App\Models\VendorPayment;
use App\Models\DealerPayment;
use App\Models\DayLoadInvoice;
use Illuminate\Support\Facades\DB;

class ProfitService
{
    private function getFormat($col) {
        return DB::connection()->getDriverName() === 'sqlite' 
            ? "strftime('%Y-%W', " . $col . ")" 
            : "DATE_FORMAT(" . $col . ", '%Y-%u')";
    }

    private function getMonthFormat($col) {
        return DB::connection()->getDriverName() === 'sqlite' 
            ? "strftime('%Y-%m', " . $col . ")" 
            : "DATE_FORMAT(" . $col . ", '%Y-%m')";
    }

    public function getWeeklyBreakdown(): array
    {
        $startDate = now()->subWeeks(4)->startOfWeek();
        $weekFormat = $this->getFormat('period_end');
        $weekFormatDate = $this->getFormat('date');
        $weekFormatEmi = $this->getFormat('due_date');

        $wBills = WeeklyBill::selectRaw($weekFormat . " as week_key, SUM(net_amount) as amount")
            ->whereNotIn('payment_mode', ['Credit', 'Pending'])
            ->whereDate('period_end', '>=', $startDate)
            ->groupByRaw($weekFormat)->get()->keyBy('week_key');

        $dBills = DailyBill::selectRaw($weekFormatDate . " as week_key, SUM(net_amount) as amount")
            ->whereNotIn('payment_mode', ['Credit', 'Pending'])
            ->whereDate('date', '>=', $startDate)
            ->groupByRaw($weekFormatDate)->get()->keyBy('week_key');

        $cPayments = CustomerPayment::selectRaw($weekFormatDate . " as week_key, SUM(amount) as amount")
            ->whereDate('date', '>=', $startDate)
            ->groupByRaw($weekFormatDate)->get()->keyBy('week_key');

        $purchases = Purchase::selectRaw($weekFormatDate . " as week_key, SUM(total_amount) as amount")
            ->whereNotIn('payment_mode', ['Credit', 'Pending'])
            ->whereDate('date', '>=', $startDate)
            ->groupByRaw($weekFormatDate)->get()->keyBy('week_key');

        $vPayments = VendorPayment::selectRaw($weekFormatDate . " as week_key, SUM(amount) as amount")
            ->whereDate('date', '>=', $startDate)
            ->groupByRaw($weekFormatDate)->get()->keyBy('week_key');

        $dPayments = DealerPayment::selectRaw($weekFormatDate . " as week_key, SUM(amount) as amount")
            ->whereDate('date', '>=', $startDate)
            ->whereNull('invoice_id')
            ->groupByRaw($weekFormatDate)->get()->keyBy('week_key');

        $dlInvoices = DayLoadInvoice::selectRaw($this->getFormat('invoice_date') . " as week_key, SUM(total_amount) as amount")
            ->whereDate('invoice_date', '>=', $startDate)
            ->groupByRaw($this->getFormat('invoice_date'))->get()->keyBy('week_key');

        $expenses = Expense::selectRaw($weekFormatDate . " as week_key, SUM(amount) as amount")
            ->whereDate('date', '>=', $startDate)
            ->groupByRaw($weekFormatDate)->get()->keyBy('week_key');

        $emis = Emi::selectRaw($weekFormatEmi . " as week_key, SUM(amount) as amount")
            ->whereIn('status', ['Paid', 'Overdue'])
            ->whereDate('due_date', '>=', $startDate)
            ->groupByRaw($weekFormatEmi)->get()->keyBy('week_key');

        $allKeys = collect([])
            ->merge($wBills->keys())->merge($dBills->keys())->merge($cPayments->keys())
            ->merge($purchases->keys())->merge($vPayments->keys())->merge($dPayments->keys())
            ->merge($dlInvoices->keys())->merge($expenses->keys())->merge($emis->keys())
            ->unique()->sort();

        return $allKeys->map(function ($wk) use ($wBills, $dBills, $cPayments, $purchases, $vPayments, $dPayments, $dlInvoices, $expenses, $emis) {
            // INFLOW: money we RECEIVE (customer bills + dealer bills + customer payments + dealer payments + day-load invoices)
            $inflow = (float)($wBills[$wk]->amount ?? 0)
                    + (float)($dBills[$wk]->amount ?? 0)
                    + (float)($cPayments[$wk]->amount ?? 0)
                    + (float)($dPayments[$wk]->amount ?? 0)
                    + (float)($dlInvoices[$wk]->amount ?? 0);

            // OUTFLOW: money we SPEND (purchases from vendors + vendor payments + expenses + EMIs)
            $outflow = (float)($purchases[$wk]->amount ?? 0)
                     + (float)($vPayments[$wk]->amount ?? 0)
                     + (float)($expenses[$wk]->amount ?? 0)
                     + (float)($emis[$wk]->amount ?? 0);
            
            return [
                'week'     => 'Week ' . substr($wk, -2),
                'revenue'  => $inflow,
                'purchase' => (float)($purchases[$wk]->amount ?? 0) + (float)($vPayments[$wk]->amount ?? 0),
                'expenses' => (float)($expenses[$wk]->amount ?? 0) + (float)($emis[$wk]->amount ?? 0),
                'profit'   => $inflow - $outflow,
            ];
        })->values()->toArray();
    }

    public function getMonthlyTrend(int $months = 6): array
    {
        $startDate = now()->subMonths($months)->startOfMonth();
        $monthFormat = $this->getMonthFormat('period_end');
        $monthFormatDate = $this->getMonthFormat('date');

        $wBills = WeeklyBill::selectRaw($monthFormat . " as month_key, SUM(net_amount) as amount")
            ->whereNotIn('payment_mode', ['Credit', 'Pending'])
            ->whereDate('period_end', '>=', $startDate)
            ->groupByRaw($monthFormat)->get()->keyBy('month_key');

        $dBills = DailyBill::selectRaw($monthFormatDate . " as month_key, SUM(net_amount) as amount")
            ->whereNotIn('payment_mode', ['Credit', 'Pending'])
            ->whereDate('date', '>=', $startDate)
            ->groupByRaw($monthFormatDate)->get()->keyBy('month_key');

        $cPayments = CustomerPayment::selectRaw($monthFormatDate . " as month_key, SUM(amount) as amount")
            ->whereDate('date', '>=', $startDate)
            ->groupByRaw($monthFormatDate)->get()->keyBy('month_key');

        $purchases = Purchase::selectRaw($monthFormatDate . " as month_key, SUM(total_amount) as amount")
            ->whereNotIn('payment_mode', ['Credit', 'Pending'])
            ->whereDate('date', '>=', $startDate)
            ->groupByRaw($monthFormatDate)->get()->keyBy('month_key');

        $vPayments = VendorPayment::selectRaw($monthFormatDate . " as month_key, SUM(amount) as amount")
            ->whereDate('date', '>=', $startDate)
            ->groupByRaw($monthFormatDate)->get()->keyBy('month_key');

        $dPayments = DealerPayment::selectRaw($monthFormatDate . " as month_key, SUM(amount) as amount")
            ->whereDate('date', '>=', $startDate)
            ->whereNull('invoice_id')
            ->groupByRaw($monthFormatDate)->get()->keyBy('month_key');

        $dlInvoices = DayLoadInvoice::selectRaw($this->getMonthFormat('invoice_date') . " as month_key, SUM(total_amount) as amount")
            ->whereDate('invoice_date', '>=', $startDate)
            ->groupByRaw($this->getMonthFormat('invoice_date'))->get()->keyBy('month_key');

        $expenses = Expense::selectRaw($monthFormatDate . " as month_key, SUM(amount) as amount")
            ->whereDate('date', '>=', $startDate)
            ->groupByRaw($monthFormatDate)->get()->keyBy('month_key');

        $allKeys = collect([])
            ->merge($wBills->keys())->merge($dBills->keys())->merge($cPayments->keys())
            ->merge($purchases->keys())->merge($vPayments->keys())->merge($dPayments->keys())
            ->merge($dlInvoices->keys())->merge($expenses->keys())
            ->unique()->sort();

        return $allKeys->map(function($mk) use ($wBills, $dBills, $cPayments, $purchases, $vPayments, $dPayments, $dlInvoices, $expenses) {
            // INFLOW: Customer bills + Dealer bills + Customer payments + Dealer payments + Day-load invoices
            $inflow = (float)($wBills[$mk]->amount ?? 0)
                    + (float)($dBills[$mk]->amount ?? 0)
                    + (float)($cPayments[$mk]->amount ?? 0)
                    + (float)($dPayments[$mk]->amount ?? 0)
                    + (float)($dlInvoices[$mk]->amount ?? 0);

            // OUTFLOW: Purchases from Vendor + Vendor payments + Expenses
            $outflow = (float)($purchases[$mk]->amount ?? 0)
                     + (float)($vPayments[$mk]->amount ?? 0)
                     + (float)($expenses[$mk]->amount ?? 0);
            return [
                'month'  => date('M', strtotime($mk . '-01')),
                'profit' => $inflow - $outflow,
            ];
        })->values()->toArray();
    }

    public function getSummary(): array
    {
        $month = sprintf('%02d', now()->month);
        $year  = (string)now()->year;

        // INFLOW — money we RECEIVE
        $wBills    = WeeklyBill::whereMonth('period_end', $month)->whereYear('period_end', $year)->whereNotIn('payment_mode', ['Credit', 'Pending'])->sum('net_amount');
        $dBills    = DailyBill::whereMonth('date', $month)->whereYear('date', $year)->whereNotIn('payment_mode', ['Credit', 'Pending'])->sum('net_amount');
        $cPayments = CustomerPayment::whereMonth('date', $month)->whereYear('date', $year)->sum('amount');
        $dPayments   = DealerPayment::whereMonth('date', $month)->whereYear('date', $year)->whereNull('invoice_id')->sum('amount');
        $dlInvoices  = DayLoadInvoice::whereMonth('invoice_date', $month)->whereYear('invoice_date', $year)->sum('total_amount');
        $revenue     = $wBills + $dBills + $cPayments + $dPayments + $dlInvoices;
        
        // OUTFLOW — money we SPEND
        $purchases  = Purchase::whereMonth('date', $month)->whereYear('date', $year)->whereNotIn('payment_mode', ['Credit', 'Pending'])->sum('total_amount');
        $vPayments  = VendorPayment::whereMonth('date', $month)->whereYear('date', $year)->sum('amount'); // We pay Vendor
        $purchase   = $purchases + $vPayments;
        
        $expensesAmt = Expense::whereMonth('date', $month)->whereYear('date', $year)->sum('amount');
        $emisAmt     = Emi::whereIn('status', ['Paid', 'Overdue'])->whereMonth('due_date', $month)->whereYear('due_date', $year)->sum('amount');
        $expenses    = $expensesAmt + $emisAmt;

        $profit = $revenue - $purchase - $expenses;
        
        return compact('revenue', 'purchase', 'expenses', 'profit');
    }

    public function getProfitBreakdown($startDate, $endDate): array
    {
        // INFLOW — Total billed (all bills)
        $totalBilled = DailyBill::whereBetween('date', [$startDate, $endDate])->sum('net_amount')
            + WeeklyBill::whereBetween('period_end', [$startDate, $endDate])->sum('net_amount')
            + DayLoadInvoice::whereBetween('invoice_date', [$startDate, $endDate])->sum('total_amount');

        // INFLOW — Actually collected (cash sales + customer payments + dealer payments)
        $cashSales = DailyBill::whereBetween('date', [$startDate, $endDate])->whereNotIn('payment_mode', ['Credit', 'Pending'])->sum('net_amount')
            + WeeklyBill::whereBetween('period_end', [$startDate, $endDate])->whereNotIn('payment_mode', ['Credit', 'Pending'])->sum('net_amount');
        $cPayments  = CustomerPayment::whereBetween('date', [$startDate, $endDate])->sum('amount');
        $dPayments  = DealerPayment::whereBetween('date', [$startDate, $endDate])->sum('amount'); // Dealer pays US → INFLOW
        $totalCollected = $cashSales + $cPayments + $dPayments;

        // OUTFLOW — Purchases
        $totalPurchaseBilled = Purchase::whereBetween('date', [$startDate, $endDate])->sum('total_amount');
        $cashPurchases = Purchase::whereBetween('date', [$startDate, $endDate])->whereNotIn('payment_mode', ['Credit', 'Pending'])->sum('total_amount');
        $vPayments     = VendorPayment::whereBetween('date', [$startDate, $endDate])->sum('amount'); // We pay Vendor → OUTFLOW
        $totalPurchasePaid = $cashPurchases + $vPayments;

        // OUTFLOW — Expenses
        $totalExpenses = Expense::whereBetween('date', [$startDate, $endDate])->sum('amount')
            + Emi::whereIn('status', ['Paid', 'Overdue'])->whereBetween('due_date', [$startDate, $endDate])->sum('amount');

        $billedProfit      = $totalBilled    - ($totalPurchaseBilled + $totalExpenses);
        $collectedProfit   = $totalCollected - ($totalPurchasePaid   + $totalExpenses);
        $pendingCollection = $totalBilled    - $totalCollected;

        return [
            'total_billed'       => round($totalBilled, 2),
            'total_collected'    => round($totalCollected, 2),
            'total_purchase'     => round($totalPurchasePaid, 2),
            'total_expenses'     => round($totalExpenses, 2),
            'billed_profit'      => round($billedProfit, 2),
            'collected_profit'   => round($collectedProfit, 2),
            'pending_collection' => round($pendingCollection, 2),
        ];
    }
}
