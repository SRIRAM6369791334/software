<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Dealer;
use App\Models\Purchase;
use App\Models\CustomerPayment;
use App\Models\Expense;
use App\Models\WeeklyBill;
use App\Models\DailyBill;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function index(): View
    {
        $summary = [
            'total_customers'       => Customer::count(),
            'total_dealers'         => Dealer::count(),
            'total_revenue_month'   => CustomerPayment::whereMonth('date', now()->month)->sum('amount'),
            'total_purchases_month' => Purchase::whereMonth('date', now()->month)->sum('total_amount'),
            'total_expenses_month'  => Expense::whereMonth('date', now()->month)->sum('amount'),
            'pending_receivables'   => Customer::where('balance', '>', 0)->sum('balance'),
            'pending_payables'      => Dealer::where('pending_amount', '>', 0)->sum('pending_amount'),
        ];

        $topCustomers = Customer::orderByDesc('balance')->limit(5)->get();
        $topDealers   = Dealer::orderByDesc('pending_amount')->limit(5)->get();

        return view('reports.index', compact('summary', 'topCustomers', 'topDealers'));
    }

    public function dailySales(): View
    {
        $bills = DailyBill::with('customer')->orderByDesc('date')->paginate(30);
        return view('reports.sales.daily', compact('bills'));
    }

    public function weeklySales(): View
    {
        $bills = WeeklyBill::with('customer')->orderByDesc('period_end')->paginate(20);
        return view('reports.sales.weekly', compact('bills'));
    }

    public function monthlySales(): View
    {
        $sales = DailyBill::select(DB::raw('DATE_FORMAT(date, "%Y-%m") as month'), DB::raw('SUM(amount) as total'))
            ->groupBy('month')
            ->orderByDesc('month')
            ->paginate(12);
        return view('reports.sales.monthly', compact('sales'));
    }

    public function customerRanking(): View
    {
        $customers = Customer::orderByDesc('balance')->paginate(20);
        return view('reports.customers.ranking', compact('customers'));
    }

    public function purchaseAnalytics(): View
    {
        $analytics = Purchase::select('item', DB::raw('SUM(total_amount) as total'), DB::raw('SUM(quantity) as qty'))
            ->groupBy('item')
            ->get();
        return view('reports.purchases.analytics', compact('analytics'));
    }
}
