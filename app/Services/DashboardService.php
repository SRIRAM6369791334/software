<?php

namespace App\Services;

use App\Models\Customer;
use App\Models\WeeklyBill;
use App\Models\DailyBill;
use App\Models\Purchase;
use App\Models\Dealer;
use App\Models\Emi;
use Illuminate\Support\Facades\Cache;

class DashboardService
{
    public function getStats(): array
    {
        return Cache::remember('dashboard.stats', 60, function () {
            $todayRevenue   = DailyBill::whereDate('date', today())->sum('amount');
            $totalCustomers = Customer::count();
            $pendingPayments = Customer::where('balance', '>', 0)->sum('balance');
            $pendingCount    = Customer::where('balance', '>', 0)->count();
            
            $purchasesToday  = Purchase::whereDate('date', today())->sum('total_amount');
            $purchaseCount   = Purchase::whereDate('date', today())->count();
            
            $monthlyRevenue  = WeeklyBill::whereMonth('period_end', now()->month)->sum('amount') +
                               DailyBill::whereMonth('date', now()->month)->sum('amount');
                               
            $monthlyPurchase = Purchase::whereMonth('date', now()->month)->sum('total_amount');
            $activeDealers   = Dealer::where('pending_amount', '>', 0)->count();

            return compact(
                'todayRevenue', 'totalCustomers', 'pendingPayments', 'pendingCount',
                'purchasesToday', 'purchaseCount', 'monthlyRevenue', 'monthlyPurchase', 'activeDealers'
            );
        });
    }

    public function getRecentSales(int $limit = 5): \Illuminate\Database\Eloquent\Collection
    {
        // Combined recent bills
        return DailyBill::with('customer')
            ->latest('date')
            ->limit($limit)
            ->get();
    }

    public function getUpcomingEmis(int $days = 7): \Illuminate\Database\Eloquent\Collection
    {
        return Emi::where('status', 'Upcoming')
            ->whereDate('due_date', '<=', now()->addDays($days))
            ->orderBy('due_date')
            ->get();
    }
}
