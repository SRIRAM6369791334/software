<?php

namespace App\Http\Controllers;

use App\Http\Requests\Expenses\StoreExpenseRequest;
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
        $data = $request->validated();
        $data['payment_method'] = $data['payment_method'] ?? 'Cash';
        $this->service->createExpense($data);
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
        // Auto-repair old Purchase EMIs previously saved with emi_type = 'Dealer'
        Emi::where('emi_type', 'Dealer')
            ->where('loan_name', 'like', 'Purchase%')
            ->update(['emi_type' => 'Vendor']);

        $emis = Emi::with(['customer', 'dealer', 'vendor'])->orderBy('due_date')->paginate(15);
        
        $allEmis = Emi::with(['customer', 'dealer', 'vendor'])->orderBy('due_date')->get();
        
        $toReceiveEmis = [];
        $toPayEmis = [];
        
        foreach ($allEmis as $emi) {
            $entityType = $emi->emi_type ?? 'Bank Loan';
            $entityId = $emi->entity_id ?? 0;
            
            if ($entityType === 'Customer') {
                $entityName = $emi->customer ? $emi->customer->name : 'Unknown Customer';
                $entityKey = "customer_{$entityId}";
            } elseif ($entityType === 'Dealer') {
                if ($emi->dealer) {
                    $entityName = $emi->dealer->firm_name ?? $emi->dealer->name;
                    $entityKey = "dealer_{$entityId}";
                } elseif ($emi->vendor) {
                    $entityName = $emi->vendor->firm_name;
                    $entityKey = "vendor_{$entityId}";
                    $entityType = 'Vendor';
                } else {
                    $entityName = 'Unknown Dealer/Vendor';
                    $entityKey = "dealer_{$entityId}";
                }
            } elseif ($entityType === 'Vendor') {
                $entityName = $emi->vendor ? ($emi->vendor->firm_name) : 'Unknown Vendor';
                $entityKey = "vendor_{$entityId}";
            } else {
                $entityName = $emi->bank_name ?? 'Bank Loan';
                $entityKey = 'bank_' . md5($entityName);
            }
            
            // Group by direction: To Pay vs To Receive
            $isToReceive = in_array($entityType, ['Customer', 'Dealer']);
            
            if ($isToReceive) {
                if (!isset($toReceiveEmis[$entityKey])) {
                    $toReceiveEmis[$entityKey] = [
                        'name' => $entityName,
                        'type' => $entityType,
                        'total_amount' => 0,
                        'pending_amount' => 0,
                        'total_installments' => 0,
                        'pending_installments' => 0,
                        'invoices' => []
                    ];
                }
                $targetGroup = &$toReceiveEmis;
            } else {
                if (!isset($toPayEmis[$entityKey])) {
                    $toPayEmis[$entityKey] = [
                        'name' => $entityName,
                        'type' => $entityType,
                        'total_amount' => 0,
                        'pending_amount' => 0,
                        'total_installments' => 0,
                        'pending_installments' => 0,
                        'invoices' => []
                    ];
                }
                $targetGroup = &$toPayEmis;
            }
            
            $invoiceKey = $emi->loan_name ?? 'General';
            if (!isset($targetGroup[$entityKey]['invoices'][$invoiceKey])) {
                $targetGroup[$entityKey]['invoices'][$invoiceKey] = [
                    'name' => $invoiceKey,
                    'total_amount' => 0,
                    'pending_amount' => 0,
                    'installments' => []
                ];
            }
            
            $targetGroup[$entityKey]['invoices'][$invoiceKey]['installments'][] = $emi;
            
            $targetGroup[$entityKey]['total_amount'] += $emi->amount;
            $targetGroup[$entityKey]['total_installments']++;
            $targetGroup[$entityKey]['invoices'][$invoiceKey]['total_amount'] += $emi->amount;
            
            if ($emi->status !== 'Paid') {
                $targetGroup[$entityKey]['pending_amount'] += $emi->amount;
                $targetGroup[$entityKey]['pending_installments']++;
                $targetGroup[$entityKey]['invoices'][$invoiceKey]['pending_amount'] += $emi->amount;
            }
        }
        
        $groupedEmis = array_merge($toReceiveEmis, $toPayEmis);

        // Alerts: ALL upcoming EMIs (no 7-day limit)
        $alertUpcoming = Emi::with(['customer', 'dealer', 'vendor'])
            ->where('status', 'Upcoming')
            ->orderBy('due_date')
            ->get();

        // Alerts: Overdue EMIs
        $alertOverdue = Emi::with(['customer', 'dealer', 'vendor'])
            ->where('status', 'Upcoming')
            ->whereDate('due_date', '<', today())
            ->get();
            
        $upcomingToReceive = $alertUpcoming->filter(fn($emi) => in_array($emi->emi_type, ['Customer', 'Dealer']));
        $upcomingToPay     = $alertUpcoming->filter(fn($emi) => !in_array($emi->emi_type, ['Customer', 'Dealer']));
        
        $overdueToReceive  = $alertOverdue->filter(fn($emi) => in_array($emi->emi_type, ['Customer', 'Dealer']));
        $overdueToPay      = $alertOverdue->filter(fn($emi) => !in_array($emi->emi_type, ['Customer', 'Dealer']));

        $expenses = $this->service->paginatedExpenses(15);
        $totals = $this->service->totals();
        
        return view('expenses.emis.index', compact(
            'emis', 
            'toReceiveEmis', 
            'toPayEmis', 
            'groupedEmis',
            'upcomingToReceive',
            'upcomingToPay',
            'overdueToReceive',
            'overdueToPay',
            'expenses',
            'totals'
        ));
    }

    public function emisCreate(): View
    {
        $customers = \App\Models\Customer::all();
        $dealers = \App\Models\Dealer::all();
        $vendors = \App\Models\Vendor::all();
        return view('expenses.emis.create', compact('customers', 'dealers', 'vendors'));
    }

    public function storeEmi(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'emi_type' => 'required|in:Bank Loan,Customer,Dealer,Vendor',
            'entity_id' => 'nullable|integer',
            'loan_name' => 'nullable|string',
            'bank_name' => 'nullable|string',
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
        $emis = Emi::with(['customer', 'dealer', 'vendor'])
            ->where('status', 'Upcoming')
            ->whereDate('due_date', '<=', now()->addDays(7))
            ->orderBy('due_date')
            ->get();

        // Overdue EMIs
        $overdue = Emi::with(['customer', 'dealer', 'vendor'])
            ->where('status', 'Upcoming')
            ->whereDate('due_date', '<', today())
            ->get();
            
        $upcomingToReceive = $emis->filter(fn($emi) => in_array($emi->emi_type, ['Customer', 'Dealer']));
        $upcomingToPay     = $emis->filter(fn($emi) => !in_array($emi->emi_type, ['Customer', 'Dealer']));
        
        $overdueToReceive  = $overdue->filter(fn($emi) => in_array($emi->emi_type, ['Customer', 'Dealer']));
        $overdueToPay      = $overdue->filter(fn($emi) => !in_array($emi->emi_type, ['Customer', 'Dealer']));

        return view('expenses.emis.alerts', [
            'upcomingEmis' => $emis,
            'overdue' => $overdue,
            'upcomingToReceive' => $upcomingToReceive,
            'upcomingToPay' => $upcomingToPay,
            'overdueToReceive' => $overdueToReceive,
            'overdueToPay' => $overdueToPay,
        ]);
    }

    public function emisEdit(Emi $emi): View
    {
        $customers = \App\Models\Customer::all();
        $dealers = \App\Models\Dealer::all();
        $vendors = \App\Models\Vendor::all();
        return view('expenses.emis.edit', compact('emi', 'customers', 'dealers', 'vendors'));
    }

    public function updateEmi(Request $request, Emi $emi): RedirectResponse
    {
        $data = $request->validate([
            'amount' => 'required|numeric',
            'due_date' => 'required|date',
            'status' => 'required|in:Upcoming,Paid,Overdue',
        ]);
        $emi->update($data);
        return redirect()->route('expenses.emis.index')->with('success', 'EMI updated successfully.');
    }

    public function payEmi(Emi $emi): RedirectResponse
    {
        $emi->update(['status' => 'Paid']);
        return back()->with('success', 'EMI marked as paid.');
    }

    public function closeFullEmi(Emi $emi): RedirectResponse
    {
        Emi::where('loan_name', $emi->loan_name)
            ->where('status', '!=', 'Paid')
            ->update(['status' => 'Paid']);
            
        return back()->with('success', 'Entire loan closed and all EMIs marked as paid.');
    }

    public function update(Request $request, Expense $expense): RedirectResponse
    {
        $data = $request->validate([
            'category'    => 'required|in:Fuel,Salary,Transport,Utility,Misc,Purchase',
            'description' => 'required|string|max:500',
            'amount'      => 'required|numeric|min:0.01',
            'date'        => 'required|date',
            'payment_method' => 'nullable|in:Cash,Bank Transfer',
        ]);
        
        $data['payment_method'] = $data['payment_method'] ?? 'Cash';
        $expense->update($data);
        
        return back()->with('success', 'Expense updated successfully.');
    }
}
