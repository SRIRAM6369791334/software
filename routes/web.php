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
use App\Http\Controllers\Masters\RouteController;
use App\Http\Controllers\StockController;
use App\Http\Controllers\BirdBatchController;
use App\Http\Controllers\Inventory\AnalyticsController as InventoryAnalyticsController;
use App\Http\Controllers\Inventory\BatchController as InventoryBatchController;
use App\Http\Controllers\Inventory\ConsumptionController as InventoryConsumptionController;
use App\Http\Controllers\Inventory\ItemController as InventoryItemController;
use App\Http\Controllers\Inventory\MortalityController as InventoryMortalityController;
use App\Http\Controllers\Inventory\StockController as InventoryStockController;
use App\Http\Controllers\Inventory\WarehouseController as InventoryWarehouseController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/
Route::get('/login', [AuthController::class, 'showLogin'])->name('login')->middleware('guest');
Route::post('/login', [AuthController::class, 'login'])->middleware(['guest', 'throttle:10,1']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

/*
|--------------------------------------------------------------------------
| Protected Routes (Authenticated)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->group(function () {

    // Global Access (All Roles)
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/alerts', [DashboardController::class, 'alerts'])->name('dashboard.alerts');

    /*
    |--------------------------------------------------------------------------
    | Data Entry & Admin
    |--------------------------------------------------------------------------
    */
    Route::middleware(['role:admin|data_entry'])->group(function () {
        // Masters
        Route::get('masters/customers/{customer}/ledger-pdf', [CustomerController::class, 'downloadLedgerPdf'])->name('masters.customers.ledger-pdf');
        Route::get('masters/customers/{customer}/billing-history', [CustomerController::class, 'billingHistory'])->name('masters.customers.billing-history');
        Route::get('masters/customers/{customer}/payment-history', [CustomerController::class, 'paymentHistory'])->name('masters.customers.payment-history');

        Route::get('masters/dealers/{dealer}/ledger-pdf', [DealerController::class, 'downloadLedgerPdf'])->name('masters.dealers.ledger-pdf');
        Route::get('masters/dealers/{dealer}/purchase-history', [DealerController::class, 'purchaseHistory'])->name('masters.dealers.purchase-history');
        Route::get('masters/dealers/{dealer}/outstanding-report', [DealerController::class, 'outstandingReport'])->name('masters.dealers.outstanding-report');
        Route::get('masters/vendors/{vendor}/purchase-history', [VendorController::class, 'purchaseHistory'])->name('masters.vendors.purchase-history');


        Route::resource('masters/customers', CustomerController::class)->names('masters.customers');
        Route::resource('masters/dealers', DealerController::class)->names('masters.dealers');
        Route::resource('masters/vendors', VendorController::class)->names('masters.vendors');

        // Purchases
        Route::get('/purchases/entry', [PurchaseController::class, 'index'])->name('purchases.entry');
        Route::get('/purchases/invoices', [PurchaseController::class, 'invoices'])->name('purchases.invoices');
        Route::get('/purchases/export', [PurchaseController::class, 'export'])->name('purchases.export');
        Route::get('/purchases/{purchase}/print', [PurchaseController::class, 'print'])->name('purchases.print');
        Route::resource('purchases', PurchaseController::class)->except(['index']);

        // Stock & Batches
        Route::get('/stock', [StockController::class, 'index'])->name('stock.index');
        Route::post('/stock/adjust', [StockController::class, 'adjust'])->name('stock.adjust');

        Route::get('/stock/batches', [BirdBatchController::class, 'index'])->name('stock.batches.index');
        Route::post('/stock/batches', [BirdBatchController::class, 'store'])->name('stock.batches.store');
        Route::post('/stock/batches/{batch}/mortality', [BirdBatchController::class, 'recordMortality'])->name('stock.batches.mortality');

        Route::prefix('inventory')->name('inventory.')->group(function () {
            Route::get('analytics', [InventoryAnalyticsController::class, 'index'])->name('analytics');
            Route::get('stock', [InventoryStockController::class, 'index'])->name('stock.index');
            Route::get('stock/movements', [InventoryStockController::class, 'movements'])->name('stock.movements');
            Route::resource('warehouses', InventoryWarehouseController::class)->except(['show']);
            Route::resource('items', InventoryItemController::class)->except(['show']);
            Route::resource('batches', InventoryBatchController::class);
            Route::resource('consumptions', InventoryConsumptionController::class)->only(['index', 'create', 'store', 'destroy']);
            Route::resource('mortalities', InventoryMortalityController::class)->only(['index', 'create', 'store', 'destroy']);
        });
    });

    /*
    |--------------------------------------------------------------------------
    | Accountant & Admin
    |--------------------------------------------------------------------------
    */
    Route::middleware(['role:admin|accountant'])->group(function () {
        // Billing
        Route::prefix('billing')->name('billing.')->group(function () {
            Route::get('weekly/bulk', [WeeklyBillingController::class, 'bulk'])->name('weekly.bulk');
            Route::resource('weekly', WeeklyBillingController::class);
            Route::post('weekly/bulk', [WeeklyBillingController::class, 'bulkStore'])->name('weekly.bulkStore');
            Route::get('weekly/{bill}/whatsapp', [WeeklyBillingController::class, 'whatsapp'])->name('weekly.whatsapp');
            Route::get('weekly/{bill}/pdf', [WeeklyBillingController::class, 'downloadPdf'])->name('weekly.pdf');

            Route::resource('daily', DailyBillingController::class);
            Route::get('daily/gst/view', [DailyBillingController::class, 'gst'])->name('daily.gst');
            Route::get('daily/export/csv', [DailyBillingController::class, 'export'])->name('daily.export');
            Route::get('daily/{bill}/invoice', [DailyBillingController::class, 'invoice'])->name('daily.invoice');
            Route::get('daily/{bill}/pdf', [DailyBillingController::class, 'downloadPdf'])->name('daily.pdf');

            Route::get('weekly/export/csv', [WeeklyBillingController::class, 'export'])->name('weekly.export');
        });

        // Payments
        Route::prefix('payments')->name('payments.')->group(function () {
            Route::get('customers/export', [CustomerPaymentController::class, 'export'])->name('customers.export');
            Route::get('dealers/export', [DealerPaymentController::class, 'export'])->name('dealers.export');
            Route::get('dealers/{dealer}/ledger', [DealerPaymentController::class, 'ledger'])->name('dealers.ledger');

            Route::resource('customers', CustomerPaymentController::class);
            Route::resource('dealers', DealerPaymentController::class);
        });

        // Expenses & EMIs
        Route::get('expenses/emis', [ExpenseController::class, 'emisIndex'])->name('expenses.emis.index');
        Route::get('expenses/emis/create', [ExpenseController::class, 'emisCreate'])->name('expenses.emis.create');
        Route::get('expenses/emis/alerts', [ExpenseController::class, 'emisAlerts'])->name('expenses.emis.alerts');
        Route::post('expenses/emis', [ExpenseController::class, 'storeEmi'])->name('expenses.emis.store');
        Route::delete('expenses/emis/{emi}', [ExpenseController::class, 'destroyEmi'])->name('expenses.emis.destroy');
        Route::get('expenses/categories', [ExpenseController::class, 'categories'])->name('expenses.categories');
        Route::get('expenses/export/csv', [ExpenseController::class, 'export'])->name('expenses.export');
        Route::resource('expenses', ExpenseController::class);

        // Profit Analysis
        Route::prefix('profit')->name('profit.')->group(function () {
            Route::get('/', [ProfitController::class, 'index'])->name('index');
            Route::get('/monthly', [ProfitController::class, 'monthly'])->name('monthly');
            Route::get('/expense-vs-income', [ProfitController::class, 'expenseVsIncome'])->name('expense-vs-income');
            Route::get('/batch', [ProfitController::class, 'batch'])->name('batch');
            Route::get('/order-wise', [ProfitController::class, 'orderWise'])->name('order-wise');
            Route::get('/comparison', [ProfitController::class, 'comparison'])->name('comparison');
            Route::get('/export/csv', [ProfitController::class, 'export'])->name('export');
            Route::get('/export/pdf', [ProfitController::class, 'exportPdf'])->name('export-pdf');
        });

        // Reports
        Route::prefix('reports')->name('reports.')->group(function () {
            Route::get('/', [ReportController::class, 'index'])->name('index');
            Route::get('/sales/daily', [ReportController::class, 'salesDaily'])->name('sales.daily');
            Route::get('/sales/weekly', [ReportController::class, 'salesWeekly'])->name('sales.weekly');
            Route::get('/sales/monthly', [ReportController::class, 'salesMonthly'])->name('sales.monthly');
            Route::get('/purchases/daily', [ReportController::class, 'purchasesDaily'])->name('purchases.daily');
            Route::get('/purchases/weekly', [ReportController::class, 'purchasesWeekly'])->name('purchases.weekly');
            Route::get('/purchases/monthly', [ReportController::class, 'purchasesMonthly'])->name('purchases.monthly');
            Route::get('/purchases/vendor-analytics', [ReportController::class, 'vendorAnalytics'])->name('purchases.vendor-analytics');
            Route::get('/customers/ranking', [ReportController::class, 'customerRanking'])->name('customers.ranking');
            Route::get('/purchases/analytics', [ReportController::class, 'purchaseAnalytics'])->name('purchases.analytics');
            Route::get('/sales/export-pdf', [ReportController::class, 'exportSalesPDF'])->name('sales.export-pdf');
            Route::get('/purchases/export-pdf', [ReportController::class, 'exportPurchasesPDF'])->name('purchases.export-pdf');
        });
    });

    /*
    |--------------------------------------------------------------------------
    | Delivery Staff & Admin
    |--------------------------------------------------------------------------
    */
    Route::middleware(['role:admin|delivery_staff'])->group(function () {
        Route::prefix('routes')->name('routes.')->group(function () {
            Route::get('/', [RouteController::class, 'index'])->name('index');
            Route::post('/', [RouteController::class, 'store'])->name('store');
            Route::post('/vehicles', [RouteController::class, 'storeVehicle'])->name('vehicles.store');
            Route::post('/drivers', [RouteController::class, 'storeDriver'])->name('drivers.store');
        });
    });

    /*
    |--------------------------------------------------------------------------
    | Admin Only
    |--------------------------------------------------------------------------
    */
    Route::middleware(['role:admin'])->prefix('admin')->name('admin.')->group(function () {
        Route::resource('users', UserManagementController::class);
        Route::post('users/{user}/toggle-status', [UserManagementController::class, 'toggleStatus'])->name('users.toggle-status');
        Route::get('activity-logs', [UserManagementController::class, 'activityLogs'])->name('activity-logs');
    });
});
