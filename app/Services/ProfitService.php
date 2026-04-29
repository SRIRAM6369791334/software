<?php

namespace App\Services;

use App\Models\WeeklyBill;
use App\Models\DailyBill;
use App\Models\Purchase;
use App\Models\Expense;
use App\Models\Emi;
use Illuminate\Support\Facades\DB;

class ProfitService
{
    public function getWeeklyBreakdown(): array
    {
        $startDate = now()->subWeeks(4)->startOfWeek();
        $isSqlite = DB::connection()->getDriverName() === 'sqlite';
        $weekFormat = $isSqlite ? "strftime('%%Y-%%W', %s)" : "DATE_FORMAT(%s, '%%Y-%%u')";

        $weeklyBills = WeeklyBill::selectRaw(sprintf("$weekFormat as week_key, SUM(net_amount) as revenue", 'period_end'))
            ->whereDate('period_end', '>=', $startDate)
            ->groupByRaw(sprintf($weekFormat, 'period_end'))
            ->get()
            ->keyBy('week_key');

        $dailyBills = DailyBill::selectRaw(sprintf("$weekFormat as week_key, SUM(net_amount) as revenue", 'date'))
            ->whereDate('date', '>=', $startDate)
            ->groupByRaw(sprintf($weekFormat, 'date'))
            ->get()
            ->keyBy('week_key');

        $purchaseTotals = Purchase::selectRaw(sprintf("$weekFormat as week_key, SUM(total_amount) as total_purchase", 'date'))
            ->whereDate('date', '>=', $startDate)
            ->groupByRaw(sprintf($weekFormat, 'date'))
            ->get()
            ->keyBy('week_key');

        $expenseTotals = Expense::selectRaw(sprintf("$weekFormat as week_key, SUM(amount) as total_expense", 'date'))
            ->whereDate('date', '>=', $startDate)
            ->groupByRaw(sprintf($weekFormat, 'date'))
            ->get()
            ->keyBy('week_key');

        $emiTotals = Emi::selectRaw(sprintf("$weekFormat as week_key, SUM(amount) as total_emi", 'due_date'))
            ->where('status', '!=', 'Upcoming')
            ->whereDate('due_date', '>=', $startDate)
            ->groupByRaw(sprintf($weekFormat, 'due_date'))
            ->get()
            ->keyBy('week_key');

        // Merge all keys to ensure we cover all weeks
        $allKeys = collect([])
            ->merge($weeklyBills->keys())
            ->merge($dailyBills->keys())
            ->merge($purchaseTotals->keys())
            ->merge($expenseTotals->keys())
            ->merge($emiTotals->keys())
            ->unique()
            ->sort();

        return $allKeys->map(function ($weekKey) use ($weeklyBills, $dailyBills, $purchaseTotals, $expenseTotals, $emiTotals) {
            $revenue  = (float) ($weeklyBills[$weekKey]->revenue ?? 0) + (float) ($dailyBills[$weekKey]->revenue ?? 0);
            $purchase = (float) ($purchaseTotals[$weekKey]->total_purchase ?? 0);
            $expense  = (float) ($expenseTotals[$weekKey]->total_expense ?? 0) + (float) ($emiTotals[$weekKey]->total_emi ?? 0);
            
            return [
                'week'     => 'Week ' . substr($weekKey, -2),
                'revenue'  => $revenue,
                'purchase' => $purchase,
                'expenses' => $expense,
                'profit'   => $revenue - $purchase - $expense,
            ];
        })->values()->toArray();
    }

    public function getMonthlyTrend(int $months = 6): array
    {
        $startDate = now()->subMonths($months)->startOfMonth();
        $isSqlite = DB::connection()->getDriverName() === 'sqlite';
        $monthFormat = $isSqlite ? "strftime('%%Y-%%m', %s)" : "DATE_FORMAT(%s, '%%Y-%%m')";

        $weeklyRevenue = WeeklyBill::selectRaw(sprintf("$monthFormat as month_key, SUM(net_amount) as revenue", 'period_end'))
            ->whereDate('period_end', '>=', $startDate)
            ->groupByRaw(sprintf($monthFormat, 'period_end'))
            ->get()
            ->keyBy('month_key');

        $dailyRevenue = DailyBill::selectRaw(sprintf("$monthFormat as month_key, SUM(net_amount) as revenue", 'date'))
            ->whereDate('date', '>=', $startDate)
            ->groupByRaw(sprintf($monthFormat, 'date'))
            ->get()
            ->keyBy('month_key');

        $purchaseTotals = Purchase::selectRaw(sprintf("$monthFormat as month_key, SUM(total_amount) as total_purchase", 'date'))
            ->whereDate('date', '>=', $startDate)
            ->groupByRaw(sprintf($monthFormat, 'date'))
            ->get()
            ->keyBy('month_key');

        $expenseTotals = Expense::selectRaw(sprintf("$monthFormat as month_key, SUM(amount) as total_expense", 'date'))
            ->whereDate('date', '>=', $startDate)
            ->groupByRaw(sprintf($monthFormat, 'date'))
            ->get()
            ->keyBy('month_key');

        $allKeys = collect([])
            ->merge($weeklyRevenue->keys())
            ->merge($dailyRevenue->keys())
            ->merge($purchaseTotals->keys())
            ->merge($expenseTotals->keys())
            ->unique()
            ->sort();

        return $allKeys->map(fn($monthKey) => [
            'month'  => date('M', strtotime($monthKey . '-01')),
            'profit' => (float) ($weeklyRevenue[$monthKey]->revenue ?? 0) + 
                       (float) ($dailyRevenue[$monthKey]->revenue ?? 0) - 
                       (float) ($purchaseTotals[$monthKey]->total_purchase ?? 0) - 
                       (float) ($expenseTotals[$monthKey]->total_expense ?? 0),
        ])->values()->toArray();
    }

    public function getSummary(): array
    {
        $month = sprintf('%02d', now()->month);
        $year  = (string)now()->year;

        $revenue  = WeeklyBill::whereMonth('period_end', $month)->whereYear('period_end', $year)->sum('net_amount') +
                    DailyBill::whereMonth('date', $month)->whereYear('date', $year)->sum('net_amount');
        
        $purchase = Purchase::whereMonth('date', $month)->whereYear('date', $year)->sum('total_amount');
        
        $expenses = Expense::whereMonth('date', $month)->whereYear('date', $year)->sum('amount') +
                    Emi::whereIn('status', ['Paid', 'Overdue'])
                       ->whereMonth('due_date', $month)
                       ->whereYear('due_date', $year)
                       ->sum('amount');

        $profit   = $revenue - $purchase - $expenses;
        
        return compact('revenue', 'purchase', 'expenses', 'profit');
    }
}
