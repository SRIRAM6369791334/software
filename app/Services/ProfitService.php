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

        $weeklyBills = WeeklyBill::selectRaw("DATE_FORMAT(period_end, '%Y-%u') as week_key, SUM(amount) as revenue")
            ->whereDate('period_end', '>=', $startDate)
            ->groupByRaw("DATE_FORMAT(period_end, '%Y-%u')")
            ->get()
            ->keyBy('week_key');

        $dailyBills = DailyBill::selectRaw("DATE_FORMAT(date, '%Y-%u') as week_key, SUM(amount) as revenue")
            ->whereDate('date', '>=', $startDate)
            ->groupByRaw("DATE_FORMAT(date, '%Y-%u')")
            ->get()
            ->keyBy('week_key');

        $purchaseTotals = Purchase::selectRaw("DATE_FORMAT(date, '%Y-%u') as week_key, SUM(total_amount) as total_purchase")
            ->whereDate('date', '>=', $startDate)
            ->groupByRaw("DATE_FORMAT(date, '%Y-%u')")
            ->get()
            ->keyBy('week_key');

        $expenseTotals = Expense::selectRaw("DATE_FORMAT(date, '%Y-%u') as week_key, SUM(amount) as total_expense")
            ->whereDate('date', '>=', $startDate)
            ->groupByRaw("DATE_FORMAT(date, '%Y-%u')")
            ->get()
            ->keyBy('week_key');

        $emiTotals = Emi::selectRaw("DATE_FORMAT(due_date, '%Y-%u') as week_key, SUM(amount) as total_emi")
            ->where('status', '!=', 'Upcoming')
            ->whereDate('due_date', '>=', $startDate)
            ->groupByRaw("DATE_FORMAT(due_date, '%Y-%u')")
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

        $weeklyRevenue = WeeklyBill::selectRaw("DATE_FORMAT(period_end, '%Y-%m') as month_key, SUM(amount) as revenue")
            ->whereDate('period_end', '>=', $startDate)
            ->groupByRaw("DATE_FORMAT(period_end, '%Y-%m')")
            ->get()
            ->keyBy('month_key');

        $dailyRevenue = DailyBill::selectRaw("DATE_FORMAT(date, '%Y-%m') as month_key, SUM(amount) as revenue")
            ->whereDate('date', '>=', $startDate)
            ->groupByRaw("DATE_FORMAT(date, '%Y-%m')")
            ->get()
            ->keyBy('month_key');

        $purchaseTotals = Purchase::selectRaw("DATE_FORMAT(date, '%Y-%m') as month_key, SUM(total_amount) as total_purchase")
            ->whereDate('date', '>=', $startDate)
            ->groupByRaw("DATE_FORMAT(date, '%Y-%m')")
            ->get()
            ->keyBy('month_key');

        $expenseTotals = Expense::selectRaw("DATE_FORMAT(date, '%Y-%m') as month_key, SUM(amount) as total_expense")
            ->whereDate('date', '>=', $startDate)
            ->groupByRaw("DATE_FORMAT(date, '%Y-%m')")
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
        $month = now()->month;
        $year  = now()->year;

        $revenue  = WeeklyBill::whereMonth('period_end', $month)->whereYear('period_end', $year)->sum('amount') +
                    DailyBill::whereMonth('date', $month)->whereYear('date', $year)->sum('amount');
        
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
