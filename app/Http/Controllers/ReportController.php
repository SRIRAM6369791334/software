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
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Carbon\Carbon;

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

    public function salesDaily(Request $request)
    {
        $date = $request->get('date', today()->toDateString());
        $dailyBills = DailyBill::with('customer')
            ->whereDate('date', $date)
            ->get();

        $totalSale    = $dailyBills->sum('amount');
        $totalGST     = $dailyBills->sum('gst_amount');
        $cashSales    = $dailyBills->where('payment_mode', 'cash')->sum('amount');
        $creditSales  = $dailyBills->where('payment_mode', 'credit')->sum('amount');

        return view('reports.sales.daily', compact(
            'dailyBills', 'totalSale', 'totalGST', 'cashSales', 'creditSales', 'date'
        ));
    }

    public function salesWeekly(Request $request)
    {
        $startDate = $request->get('start', now()->startOfWeek()->toDateString());
        $endDate   = $request->get('end',   now()->endOfWeek()->toDateString());

        $bills = WeeklyBill::with('customer')
            ->whereBetween('period_start', [$startDate, $endDate])
            ->get();

        $totalSale = $bills->sum('amount');
        $routeWise = $bills->groupBy('customer.route')
            ->map(fn($group) => $group->sum('amount'));

        return view('reports.sales.weekly', compact(
            'bills', 'totalSale', 'routeWise', 'startDate', 'endDate'
        ));
    }

    public function salesMonthly(Request $request)
    {
        $month = $request->get('month', now()->month);
        $year  = $request->get('year',  now()->year);

        $bills = WeeklyBill::with('customer')
            ->whereMonth('period_start', $month)
            ->whereYear('period_start', $year)
            ->get();

        $totalSale   = $bills->sum('amount');
        $customerRanking = $bills->groupBy('customer_id')
            ->map(fn($g) => ['customer' => $g->first()->customer, 'total' => $g->sum('amount')])
            ->sortByDesc('total')
            ->values();

        return view('reports.sales.monthly', compact(
            'bills', 'totalSale', 'customerRanking', 'month', 'year'
        ));
    }

    public function purchasesDaily(Request $request)
    {
        $date = $request->get('date', today()->toDateString());
        $purchases = Purchase::with('vendor')
            ->whereDate('date', $date)
            ->get();

        $totalAmount  = $purchases->sum('total_amount');
        $totalGST     = $purchases->sum('gst_amount');
        $categoryWise = $purchases->groupBy('item')
            ->map(fn($g) => $g->sum('total_amount'));

        return view('reports.purchases.daily', compact(
            'purchases', 'totalAmount', 'totalGST', 'categoryWise', 'date'
        ));
    }

    public function purchasesWeekly(Request $request)
    {
        $startDate = $request->get('start', now()->startOfWeek()->toDateString());
        $endDate   = $request->get('end',   now()->endOfWeek()->toDateString());

        $purchases = Purchase::with('vendor')
            ->whereBetween('date', [$startDate, $endDate])
            ->get();

        $totalAmount  = $purchases->sum('total_amount');
        $vendorWise   = $purchases->groupBy('vendor_id')
            ->map(fn($g) => [
                'vendor' => $g->first()->vendor ? $g->first()->vendor->firm_name : 'N/A',
                'total'  => $g->sum('total_amount')
            ]);

        return view('reports.purchases.weekly', compact(
            'purchases', 'totalAmount', 'vendorWise', 'startDate', 'endDate'
        ));
    }

    public function purchasesMonthly(Request $request)
    {
        $month = $request->get('month', now()->month);
        $year  = $request->get('year',  now()->year);

        $purchases = Purchase::with('vendor')
            ->whereMonth('date', $month)
            ->whereYear('date', $year)
            ->get();

        $totalAmount  = $purchases->sum('total_amount');
        $itemWise     = $purchases->groupBy('item')
            ->map(fn($g) => $g->sum('total_amount'));
        $vendorWise   = $purchases->groupBy('vendor_id')
            ->map(fn($g) => [
                'vendor' => $g->first()->vendor ? $g->first()->vendor->firm_name : 'N/A',
                'total'  => $g->sum('total_amount')
            ]);

        return view('reports.purchases.monthly', compact(
            'purchases', 'totalAmount', 'itemWise', 'vendorWise', 'month', 'year'
        ));
    }

    public function vendorAnalytics()
    {
        $vendorWise = Purchase::with('vendor')
            ->select('vendor_id', DB::raw('SUM(total_amount) as total'), DB::raw('COUNT(*) as orders'))
            ->groupBy('vendor_id')->orderByDesc('total')->get();
        return view('reports.purchases.vendor-analytics', compact('vendorWise'));
    }

    public function exportSalesPDF(Request $request)
    {
        $date = $request->get('date');
        $start = $request->get('start');
        $end = $request->get('end');
        $month = $request->get('month');
        $year = $request->get('year');

        $data = [];
        $title = "Sales Report";

        if ($date) {
            $data = DailyBill::with('customer')->whereDate('date', $date)->get();
            $title = "Daily Sales Report - " . $date;
        } elseif ($start && $end) {
            $data = WeeklyBill::with('customer')->whereBetween('period_start', [$start, $end])->get();
            $title = "Weekly Sales Report (" . $start . " to " . $end . ")";
        } elseif ($month && $year) {
            $data = WeeklyBill::with('customer')->whereMonth('period_start', $month)->whereYear('period_start', $year)->get();
            $title = "Monthly Sales Report - " . date('F', mktime(0, 0, 0, $month, 1)) . " " . $year;
        }

        $pdf = Pdf::loadView('reports.pdf.sales', compact('data', 'title'));
        return $pdf->download(strtolower(str_replace(' ', '_', $title)) . '.pdf');
    }

    public function exportPurchasesPDF(Request $request)
    {
        $date = $request->get('date');
        $start = $request->get('start');
        $end = $request->get('end');
        $month = $request->get('month');
        $year = $request->get('year');

        $data = [];
        $title = "Purchase Report";

        if ($date) {
            $data = Purchase::with('vendor')->whereDate('date', $date)->get();
            $title = "Daily Purchase Report - " . $date;
        } elseif ($start && $end) {
            $data = Purchase::with('vendor')->whereBetween('date', [$start, $end])->get();
            $title = "Weekly Purchase Report (" . $start . " to " . $end . ")";
        } elseif ($month && $year) {
            $data = Purchase::with('vendor')->whereMonth('date', $month)->whereYear('date', $year)->get();
            $title = "Monthly Purchase Report - " . date('F', mktime(0, 0, 0, $month, 1)) . " " . $year;
        }

        $pdf = Pdf::loadView('reports.pdf.purchases', compact('data', 'title'));
        return $pdf->download(strtolower(str_replace(' ', '_', $title)) . '.pdf');
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
