<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreExpenseRequest;
use App\Models\Expense;
use App\Services\ExportService;
use App\Services\ExpenseService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Models\Emi;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ExpenseController extends Controller
{
    public function __construct(
        private ExpenseService $service,
        private ExportService  $exporter,
    ) {}

    public function index(): View
    {
        $expenses = $this->service->paginatedExpenses(15);
        $emis     = $this->service->allEmis();
        $totals   = $this->service->totals();
        return view('expenses.index', compact('expenses', 'emis', 'totals'));
    }

    public function store(StoreExpenseRequest $request): RedirectResponse
    {
        $this->service->createExpense($request->validated());
        return back()->with('success', 'Expense added successfully.');
    }

    public function destroy(Expense $expense): RedirectResponse
    {
        $this->service->deleteExpense($expense);
        return back()->with('success', 'Expense deleted.');
    }

    public function export(): StreamedResponse
    {
        $rows = $this->service->allExpensesForExport()->map(fn($e) => [
            $e->date->format('Y-m-d'), $e->category, $e->description, $e->amount,
        ]);
        return $this->exporter->streamCsv(
            'expenses',
            ['Date', 'Category', 'Description', 'Amount'],
            $rows
        );
    }

    public function categories(): View
    {
        $categories = Expense::select('category', DB::raw('count(*) as count'), DB::raw('sum(amount) as total'))
            ->groupBy('category')
            ->get();
        return view('expenses.categories.index', compact('categories'));
    }

    public function create(): View
    {
        return view('expenses.create');
    }

    public function emisIndex(): View
    {
        $emis = Emi::orderBy('due_date')->paginate(15);
        return view('expenses.emis.index', compact('emis'));
    }

    public function emisCreate(): View
    {
        return view('expenses.emis.create');
    }

    public function storeEmi(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'item' => 'required|string',
            'amount' => 'required|numeric',
            'due_date' => 'required|date',
            'status' => 'required|in:Upcoming,Paid,Overdue',
        ]);
        Emi::create($data);
        return redirect()->route('expenses.emis.index')->with('success', 'EMI recorded.');
    }

    public function destroyEmi(Emi $emi): RedirectResponse
    {
        $emi->delete();
        return back()->with('success', 'EMI deleted.');
    }

    public function emisAlerts(): View
    {
        // EMIs due in the NEXT 7 days
        $emis = Emi::where('status', 'Upcoming')
            ->whereDate('due_date', '<=', now()->addDays(7))
            ->orderBy('due_date')
            ->get();

        // Overdue EMIs
        $overdue = Emi::where('status', 'Upcoming')
            ->whereDate('due_date', '<', today())
            ->get();

        return view('expenses.emis.alerts', compact('emis', 'overdue'));
    }
}
