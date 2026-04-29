<?php

namespace App\Http\Controllers;

use App\Services\ExportService;
use App\Services\ProfitService;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ProfitController extends Controller
{
    public function __construct(
        private ProfitService $service,
        private ExportService $exporter,
    ) {}

    public function index(): View
    {
        $weeklyData   = $this->service->getWeeklyBreakdown();
        $monthlyData  = $this->service->getMonthlyTrend(6);
        $summary      = $this->service->getSummary();
        return view('profit.index', compact('weeklyData', 'monthlyData', 'summary'));
    }

    public function monthly(): View
    {
        $monthlyTrend = $this->service->getMonthlyTrend(12);
        return view('profit.monthly', compact('monthlyTrend'));
    }

    public function expenseVsIncome(): View
    {
        $summary = $this->service->getSummary();
        $weeklyData = $this->service->getWeeklyBreakdown();
        return view('profit.expense-vs-income', compact('summary', 'weeklyData'));
    }

    public function batch(): View
    {
        return view('profit.batch');
    }

    public function orderWise(): View
    {
        return view('profit.order-wise');
    }

    public function comparison(): View
    {
        $summary = $this->service->getSummary();
        return view('profit.comparison', compact('summary'));
    }

    public function export(): StreamedResponse
    {
        $rows = collect($this->service->getWeeklyBreakdown())->map(fn($w) => [
            $w['week'], $w['revenue'], $w['purchase'], $w['expenses'], $w['profit'],
        ]);
        return $this->exporter->streamCsv(
            'profit-report',
            ['Week', 'Revenue', 'Purchases', 'Expenses', 'Profit'],
            $rows
        );
    }
}
