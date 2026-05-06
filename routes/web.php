<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Masters\CustomerController;
use App\Http\Controllers\Masters\DealerController;
use App\Http\Controllers\Masters\VendorController;
use App\Http\Controllers\Billing\WeeklyBillingController;
use App\Http\Controllers\Billing\DailyBillingController;
use App\Http\Controllers\Purchases\PurchaseController;
use App\Http\Controllers\Payments\CustomerPaymentController;
use App\Http\Controllers\Payments\DealerPaymentController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\ProfitController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\Admin\UserManagementController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public Routes (Unauthenticated)
|--------------------------------------------------------------------------
*/
Route::get('/login',  [AuthController::class, 'showLogin'])->name('login')->middleware('guest');
Route::post('/login', [AuthController::class, 'login'])->middleware(['guest', 'throttle:10,1']);
Route::post('/logout',[AuthController::class, 'logout'])->name('logout')->middleware('auth');

/*
|--------------------------------------------------------------------------
| Protected Routes (All authenticated users — viewer minimum)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:viewer'])->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/alerts', [DashboardController::class, 'alerts'])->name('dashboard.alerts');
    
    // Reports sub-routes
    Route::get('/reports', [ReportController::class, 'index'])->name('reports');
    Route::get('/reports/sales/daily', [ReportController::class, 'salesDaily'])->name('reports.sales.daily');
    Route::get('/reports/sales/weekly', [ReportController::class, 'salesWeekly'])->name('reports.sales.weekly');
    Route::get('/reports/sales/monthly', [ReportController::class, 'salesMonthly'])->name('reports.sales.monthly');
    Route::get('/reports/sales/customer-ranking', [ReportController::class, 'customerRanking'])->name('reports.sales.customer-ranking');
    
    Route::get('/reports/purchases/daily', [ReportController::class, 'purchasesDaily'])->name('reports.purchases.daily');
    Route::get('/reports/purchases/weekly', [ReportController::class, 'purchasesWeekly'])->name('reports.purchases.weekly');
    Route::get('/reports/purchases/monthly', [ReportController::class, 'purchasesMonthly'])->name('reports.purchases.monthly');
    Route::get('/reports/purchases/vendor-analytics', [ReportController::class, 'vendorAnalytics'])->name('reports.purchases.vendor-analytics');

    // Export Routes
    Route::get('/reports/sales/export-pdf',     [ReportController::class, 'exportSalesPDF'])->name('reports.sales.export-pdf');
    Route::get('/reports/purchases/export-pdf', [ReportController::class, 'exportPurchasesPDF'])->name('reports.purchases.export-pdf');
});

/*
|--------------------------------------------------------------------------
| Staff Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:staff'])->group(function () {
    // Masters — Customers
    Route::get('/masters/customers',          [CustomerController::class, 'index'])->name('masters.customers.index');
    Route::get('/masters/customers/create',   [CustomerController::class, 'create'])->name('masters.customers.create');
    Route::post('/masters/customers',         [CustomerController::class, 'store'])->name('masters.customers.store');
    Route::get('/masters/customers/{customer}', [CustomerController::class, 'show'])->name('masters.customers.show');
    Route::get('/masters/customers/{customer}/edit', [CustomerController::class, 'edit'])->name('masters.customers.edit');
    Route::put('/masters/customers/{customer}',[CustomerController::class, 'update'])->name('masters.customers.update');
    Route::delete('/masters/customers/{customer}',[CustomerController::class,'destroy'])->name('masters.customers.destroy');
    Route::get('/masters/customers/{customer}/billing-history', [CustomerController::class, 'billingHistory'])->name('masters.customers.billing-history');
    Route::get('/masters/customers/{customer}/payment-history', [CustomerController::class, 'paymentHistory'])->name('masters.customers.payment-history');
    Route::get('/masters/customers/{customer}/ledger-pdf', [CustomerController::class, 'downloadLedgerPdf'])->name('masters.customers.ledger-pdf');

    // Masters — Dealers
    Route::get('/masters/dealers',           [DealerController::class, 'index'])->name('masters.dealers.index');
    Route::get('/masters/dealers/create',    [DealerController::class, 'create'])->name('masters.dealers.create');
    Route::post('/masters/dealers',          [DealerController::class, 'store'])->name('masters.dealers.store');
    Route::get('/masters/dealers/{dealer}',   [DealerController::class, 'show'])->name('masters.dealers.show');
    Route::get('/masters/dealers/{dealer}/edit', [DealerController::class, 'edit'])->name('masters.dealers.edit');
    Route::put('/masters/dealers/{dealer}',  [DealerController::class, 'update'])->name('masters.dealers.update');
    Route::delete('/masters/dealers/{dealer}',[DealerController::class,'destroy'])->name('masters.dealers.destroy');
    Route::get('/masters/dealers/{dealer}/purchase-history', [DealerController::class, 'purchaseHistory'])->name('masters.dealers.purchase-history');
    Route::get('/masters/dealers/{dealer}/outstanding-report', [DealerController::class, 'outstandingReport'])->name('masters.dealers.outstanding-report');
    Route::get('/masters/dealers/{dealer}/ledger-pdf', [DealerController::class, 'downloadLedgerPdf'])->name('masters.dealers.ledger-pdf');

    // Masters — Vendors
    Route::get('/masters/vendors',           [VendorController::class, 'index'])->name('masters.vendors.index');
    Route::get('/masters/vendors/create',    [VendorController::class, 'create'])->name('masters.vendors.create');
    Route::post('/masters/vendors',          [VendorController::class, 'store'])->name('masters.vendors.store');
    Route::get('/masters/vendors/{vendor}',  [VendorController::class, 'show'])->name('masters.vendors.show');
    Route::get('/masters/vendors/{vendor}/edit', [VendorController::class, 'edit'])->name('masters.vendors.edit');
    Route::put('/masters/vendors/{vendor}',  [VendorController::class, 'update'])->name('masters.vendors.update');
    Route::delete('/masters/vendors/{vendor}',[VendorController::class,'destroy'])->name('masters.vendors.destroy');
    Route::get('/masters/vendors/{vendor}/purchase-history', [VendorController::class, 'purchaseHistory'])->name('masters.vendors.purchase-history');

    // Purchases
    Route::get('/purchases/entry',          [PurchaseController::class, 'index'])->name('purchases.entry');
    Route::get('/purchases/entry/create',   [PurchaseController::class, 'create'])->name('purchases.create');
    Route::post('/purchases/entry',         [PurchaseController::class, 'store'])->name('purchases.store');
    Route::get('/purchases/entry/{purchase}', [PurchaseController::class, 'show'])->name('purchases.show');
    Route::get('/purchases/entry/{purchase}/edit', [PurchaseController::class, 'edit'])->name('purchases.edit');
    Route::put('/purchases/entry/{purchase}', [PurchaseController::class, 'update'])->name('purchases.update');
    Route::delete('/purchases/entry/{purchase}', [PurchaseController::class, 'destroy'])->name('purchases.destroy');
    Route::get('/purchases/invoices',       [PurchaseController::class, 'invoices'])->name('purchases.invoices');
    Route::get('/purchases/entry/{purchase}/print', [PurchaseController::class, 'print'])->name('purchases.print');
    Route::get('/purchases/export',         [PurchaseController::class, 'export'])->name('purchases.export');
    // Inventory - Item Master
    Route::resource('inventory/items', \App\Http\Controllers\Inventory\ItemController::class)->names([
        'index' => 'inventory.items.index',
        'create' => 'inventory.items.create',
        'store' => 'inventory.items.store',
        'show' => 'inventory.items.show',
        'edit' => 'inventory.items.edit',
        'update' => 'inventory.items.update',
        'destroy' => 'inventory.items.destroy',
    ]);

    // Inventory - Batch Management
    Route::resource('inventory/batches', \App\Http\Controllers\Inventory\BatchController::class)->names([
        'index' => 'inventory.batches.index',
        'create' => 'inventory.batches.create',
        'store' => 'inventory.batches.store',
        'show' => 'inventory.batches.show',
        'edit' => 'inventory.batches.edit',
        'update' => 'inventory.batches.update',
        'destroy' => 'inventory.batches.destroy',
    ]);

    // Inventory - Warehouse Management
    Route::resource('inventory/warehouses', \App\Http\Controllers\Inventory\WarehouseController::class)->names([
        'index' => 'inventory.warehouses.index',
        'create' => 'inventory.warehouses.create',
        'store' => 'inventory.warehouses.store',
        'edit' => 'inventory.warehouses.edit',
        'update' => 'inventory.warehouses.update',
        'destroy' => 'inventory.warehouses.destroy',
    ]);

    // Inventory - Stock Dashboard & Ledgers
    Route::get('inventory/stock', [\App\Http\Controllers\Inventory\StockController::class, 'index'])->name('inventory.stock.index');
    Route::get('inventory/stock/movements', [\App\Http\Controllers\Inventory\StockController::class, 'movements'])->name('inventory.stock.movements');

    // Inventory - Consumption Recording
    Route::resource('inventory/consumptions', \App\Http\Controllers\Inventory\ConsumptionController::class)->names([
        'index' => 'inventory.consumptions.index',
        'create' => 'inventory.consumptions.create',
        'store' => 'inventory.consumptions.store',
        'destroy' => 'inventory.consumptions.destroy',
    ]);

    // Inventory - Mortality Tracking
    Route::resource('inventory/mortalities', \App\Http\Controllers\Inventory\MortalityController::class)->names([
        'index' => 'inventory.mortalities.index',
        'create' => 'inventory.mortalities.create',
        'store' => 'inventory.mortalities.store',
        'destroy' => 'inventory.mortalities.destroy',
    ]);

    // Inventory - Performance Analytics
    Route::get('inventory/analytics', [\App\Http\Controllers\Inventory\AnalyticsController::class, 'index'])->name('inventory.analytics');
});

/*
|--------------------------------------------------------------------------
| Manager Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:manager'])->group(function () {
    // Billing
    Route::get('/billing/weekly',  [WeeklyBillingController::class, 'index'])->name('billing.weekly.index');
    Route::get('/billing/weekly/bulk', [WeeklyBillingController::class, 'bulk'])->name('billing.weekly.bulk'); // New View
    Route::post('/billing/weekly', [WeeklyBillingController::class, 'store'])->name('billing.weekly.store')->middleware('throttle:billing');
    Route::post('/billing/weekly/bulk', [WeeklyBillingController::class, 'bulkStore'])->name('billing.weekly.bulkStore')->middleware('throttle:billing');
    Route::get('/billing/weekly/{bill}', [WeeklyBillingController::class, 'show'])->name('billing.weekly.show');
    Route::get('/billing/weekly/{bill}/print', [WeeklyBillingController::class, 'print'])->name('billing.weekly.print'); // New Print View
    Route::get('/billing/weekly/{bill}/whatsapp', [WeeklyBillingController::class, 'whatsapp'])->name('billing.weekly.whatsapp');
    Route::get('/billing/weekly/export', [WeeklyBillingController::class, 'export'])->name('billing.weekly.export');
    Route::get('/billing/weekly/{bill}/pdf', [WeeklyBillingController::class, 'downloadPdf'])->name('billing.weekly.pdf');

    Route::get('/billing/daily',   [DailyBillingController::class, 'index'])->name('billing.daily.index');
    Route::get('/billing/daily/create', [DailyBillingController::class, 'create'])->name('billing.daily.create'); // New View
    Route::post('/billing/daily',  [DailyBillingController::class, 'store'])->name('billing.daily.store')->middleware('throttle:billing');
    Route::get('/billing/daily/gst', [DailyBillingController::class, 'gst'])->name('billing.daily.gst'); // New View
    Route::get('/billing/daily/export', [DailyBillingController::class, 'export'])->name('billing.daily.export');
    Route::get('/billing/daily/{bill}/invoice', [DailyBillingController::class, 'invoice'])->name('billing.daily.invoice');
    Route::get('/billing/daily/{bill}/pdf', [DailyBillingController::class, 'downloadPdf'])->name('billing.daily.pdf');

    // Payments
    Route::get('/payments/customers',        [CustomerPaymentController::class, 'index'])->name('payments.customers.index');
    Route::get('/payments/customers/create', [CustomerPaymentController::class, 'create'])->name('payments.customers.create');
    Route::post('/payments/customers',       [CustomerPaymentController::class, 'store'])->name('payments.customers.store')->middleware('throttle:payments');
    Route::get('/payments/customers/ledger',  [CustomerPaymentController::class, 'ledger'])->name('payments.customers.ledger');
    Route::get('/payments/customers/export', [CustomerPaymentController::class, 'export'])->name('payments.customers.export');

    Route::get('/payments/dealers',        [DealerPaymentController::class, 'index'])->name('payments.dealers.index');
    Route::get('/payments/dealers/create', [DealerPaymentController::class, 'create'])->name('payments.dealers.create');
    Route::post('/payments/dealers',       [DealerPaymentController::class, 'store'])->name('payments.dealers.store')->middleware('throttle:payments');
    Route::get('/payments/dealers/{dealer}/ledger', [DealerPaymentController::class, 'ledger'])->name('payments.dealers.ledger');
    Route::get('/payments/dealers/outstanding', [DealerPaymentController::class, 'outstanding'])->name('payments.dealers.outstanding');
    Route::get('/payments/dealers/export', [DealerPaymentController::class, 'export'])->name('payments.dealers.export');

    // Expenses
    Route::get('/expenses',              [ExpenseController::class, 'index'])->name('expenses.index');
    Route::get('/expenses/create',       [ExpenseController::class, 'create'])->name('expenses.create');
    Route::post('/expenses',             [ExpenseController::class, 'store'])->name('expenses.store');
    Route::get('/expenses/categories',   [ExpenseController::class, 'categories'])->name('expenses.categories');
    Route::delete('/expenses/{expense}', [ExpenseController::class, 'destroy'])->name('expenses.destroy');
    Route::get('/expenses/export',       [ExpenseController::class, 'export'])->name('expenses.export');

    // EMIs
    Route::get('/expenses/emis',         [ExpenseController::class, 'emisIndex'])->name('expenses.emis.index');
    Route::get('/expenses/emis/create',  [ExpenseController::class, 'emisCreate'])->name('expenses.emis.create');
    Route::get('/expenses/emis/alerts',  [ExpenseController::class, 'emisAlerts'])->name('expenses.emis.alerts');

    // Profit
    Route::get('/profit',               [ProfitController::class, 'index'])->name('profit.index');
    Route::get('/profit/monthly',       [ProfitController::class, 'monthly'])->name('profit.monthly');
    Route::get('/profit/expense-vs-income', [ProfitController::class, 'expenseVsIncome'])->name('profit.expense-vs-income');
    Route::get('/profit/batch',         [ProfitController::class, 'batch'])->name('profit.batch');
    Route::get('/profit/order-wise',    [ProfitController::class, 'orderWise'])->name('profit.order-wise');
    Route::get('/profit/reports/comparison', [ProfitController::class, 'comparison'])->name('profit.reports.comparison');
    Route::get('/profit/export',        [ProfitController::class, 'export'])->name('profit.export');
    Route::get('/profit/export-pdf',    [ProfitController::class, 'exportPdf'])->name('profit.export-pdf');

    // Stock
    Route::get('/stock', [\App\Http\Controllers\StockController::class, 'index'])->name('stock.index');
});

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/users', [UserManagementController::class, 'index'])->name('users.index');
    Route::post('/users/assign-role',           [UserManagementController::class, 'assignRole'])->name('users.assign-role');
    Route::delete('/user-roles/{userRole}',     [UserManagementController::class, 'removeRole'])->name('user-roles.destroy');
    Route::post('/roles',                       [UserManagementController::class, 'storeRole'])->name('roles.store');
    Route::delete('/roles/{role}',              [UserManagementController::class, 'destroyRole'])->name('roles.destroy');
});
