<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Masters\CustomerController;
use App\Http\Controllers\Masters\DealerController;
use App\Http\Controllers\Masters\VendorController;
use App\Http\Controllers\Billing\WeeklyBillingController;
use App\Http\Controllers\Billing\DailyBillingController;
use App\Http\Controllers\Billing\DayLoadBillingController;
use App\Http\Controllers\Purchases\PurchaseController;
use App\Http\Controllers\Payments\CustomerPaymentController;
use App\Http\Controllers\Payments\DealerPaymentController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\ProfitController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\Admin\UserManagementController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\PermissionController;
use App\Http\Controllers\Masters\RouteController;
use App\Http\Controllers\Inventory\AnalyticsController as InventoryAnalyticsController;
use App\Http\Controllers\Inventory\BatchController as InventoryBatchController;
use App\Http\Controllers\Inventory\ConsumptionController as InventoryConsumptionController;
use App\Http\Controllers\Inventory\ItemController as InventoryItemController;
use App\Http\Controllers\Inventory\MortalityController as InventoryMortalityController;
use App\Http\Controllers\Inventory\StockController as InventoryStockController;
use App\Http\Controllers\Inventory\WarehouseController as InventoryWarehouseController;
use Illuminate\Support\Facades\Route;

if (!function_exists('permissionResource')) {
    function permissionResource($name, $controller, $permission, $options = []) {
        $only = $options['only'] ?? ['index', 'create', 'store', 'show', 'edit', 'update', 'destroy'];
        $except = $options['except'] ?? [];
        $methods = array_diff($only, $except);
        
        $view = array_intersect($methods, ['index', 'show']);
        $create = array_intersect($methods, ['create', 'store']);
        $edit = array_intersect($methods, ['edit', 'update']);
        $delete = array_intersect($methods, ['destroy']);
        
        if (!empty($create)) {
            $r = Route::resource($name, $controller)->only($create)->middleware("permission:create $permission");
            if (isset($options['names'])) $r->names($options['names']);
        }
        if (!empty($view)) {
            $r = Route::resource($name, $controller)->only($view)->middleware("permission:view $permission");
            if (isset($options['names'])) $r->names($options['names']);
        }
        if (!empty($edit)) {
            $r = Route::resource($name, $controller)->only($edit)->middleware("permission:edit $permission");
            if (isset($options['names'])) $r->names($options['names']);
        }
        if (!empty($delete)) {
            $r = Route::resource($name, $controller)->only($delete)->middleware("permission:delete $permission");
            if (isset($options['names'])) $r->names($options['names']);
        }
    }
}

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
    Route::get('/global-search', \App\Http\Controllers\GlobalSearchController::class)->name('global.search');

    // ── Notifications ────────────────────────────────────────────────────────
    Route::prefix('notifications')->name('notifications.')->controller(App\Http\Controllers\NotificationController::class)->group(function () {
        Route::get('/', 'index')->name('index');
        Route::post('/{id}/read', 'markAsRead')->name('read');
        Route::post('/read-all', 'markAllAsRead')->name('readAll');
    });

    /*
    |--------------------------------------------------------------------------
    | Master Records
    |--------------------------------------------------------------------------
    */
    Route::middleware(['permission:view customers'])->group(function () {
        Route::get('masters/customers/{customer}/ledger-pdf', [CustomerController::class, 'downloadLedgerPdf'])->name('masters.customers.ledger-pdf');
    });
    Route::middleware(['permission:view customer bills'])->group(function () {
        Route::get('masters/customers/{customer}/billing-history', [CustomerController::class, 'billingHistory'])->name('masters.customers.billing-history');
    });
    Route::middleware(['permission:view customer payments'])->group(function () {
        Route::get('masters/customers/{customer}/payment-history', [CustomerController::class, 'paymentHistory'])->name('masters.customers.payment-history');
    });
    Route::middleware(['permission:view customer emis'])->group(function () {
        Route::get('masters/customers/{customer}/emi-history', [CustomerController::class, 'emiHistory'])->name('masters.customers.emi-history');
    });
    permissionResource('masters/customers', CustomerController::class, 'customers', ['names' => 'masters.customers']);

    Route::middleware(['permission:view dealers'])->group(function () {
        Route::get('masters/dealers/{dealer}/ledger-pdf', [DealerController::class, 'downloadLedgerPdf'])->name('masters.dealers.ledger-pdf');
        Route::get('masters/dealers/{dealer}/outstanding-report', [DealerController::class, 'outstandingReport'])->name('masters.dealers.outstanding-report');
    });
    Route::middleware(['permission:view dealer purchases'])->group(function () {
        Route::get('masters/dealers/{dealer}/purchase-history', [DealerController::class, 'purchaseHistory'])->name('masters.dealers.purchase-history');
    });
    permissionResource('masters/dealers', DealerController::class, 'dealers', ['names' => 'masters.dealers']);

    Route::middleware(['permission:view vendors'])->group(function () {
        Route::get('masters/vendors/{vendor}/history-pdf', [VendorController::class, 'downloadHistoryPdf'])->name('masters.vendors.history-pdf');
    });
    Route::middleware(['permission:view vendor purchases'])->group(function () {
        Route::get('masters/vendors/{vendor}/purchase-history', [VendorController::class, 'purchaseHistory'])->name('masters.vendors.purchase-history');
    });
    permissionResource('masters/vendors', VendorController::class, 'vendors', ['names' => 'masters.vendors']);

    /*
    |--------------------------------------------------------------------------
    | Purchases
    |--------------------------------------------------------------------------
    */
    Route::middleware(['permission:view purchases'])->group(function () {
        Route::get('/purchases/entry', [PurchaseController::class, 'index'])->name('purchases.entry');
        Route::get('/purchases/invoices', [PurchaseController::class, 'invoices'])->name('purchases.invoices');
        Route::get('/purchases/export', [PurchaseController::class, 'export'])->name('purchases.export');
        Route::get('/purchases/{purchase}/print', [PurchaseController::class, 'print'])->name('purchases.print');
    });
    permissionResource('purchases', PurchaseController::class, 'purchases', ['except' => ['index']]);

    /*
    |--------------------------------------------------------------------------
    | Inventory & Stock
    |--------------------------------------------------------------------------
    */

    Route::prefix('inventory')->name('inventory.')->group(function () {
        Route::get('analytics', [InventoryAnalyticsController::class, 'index'])->name('analytics')->middleware('permission:view analytics');
        Route::get('stock', [InventoryStockController::class, 'index'])->name('stock.index')->middleware('permission:view stock');
        Route::get('stock/movements', [InventoryStockController::class, 'movements'])->name('stock.movements')->middleware('permission:view stock');
        
        permissionResource('warehouses', InventoryWarehouseController::class, 'warehouses', ['except' => ['show']]);
        permissionResource('items', InventoryItemController::class, 'items', ['except' => ['show']]);
        permissionResource('batches', InventoryBatchController::class, 'batches');
        permissionResource('consumptions', InventoryConsumptionController::class, 'consumptions', ['only' => ['index', 'create', 'store', 'destroy']]);
        permissionResource('mortalities', InventoryMortalityController::class, 'mortalities', ['only' => ['index', 'create', 'store', 'destroy']]);
    });

    /*
    |--------------------------------------------------------------------------
    | Billing
    |--------------------------------------------------------------------------
    */
    Route::prefix('billing')->name('billing.')->group(function () {
        Route::middleware(['permission:view bills'])->group(function () {
            Route::get('day-load', [DayLoadBillingController::class, 'index'])->name('day-load.index');
            Route::get('weekly/bulk', [WeeklyBillingController::class, 'bulk'])->name('weekly.bulk');
            Route::get('weekly/{bill}/whatsapp', [WeeklyBillingController::class, 'whatsapp'])->name('weekly.whatsapp');
            Route::get('weekly/{bill}/pdf', [WeeklyBillingController::class, 'downloadPdf'])->name('weekly.pdf');
            Route::get('weekly/export/csv', [WeeklyBillingController::class, 'export'])->name('weekly.export');
            Route::get('weekly/calculate-preview', [WeeklyBillingController::class, 'calculatePreview'])->name('weekly.calculate-preview');
            
            Route::get('daily/gst/view', [DailyBillingController::class, 'gst'])->name('daily.gst');
            Route::get('daily/export/csv', [DailyBillingController::class, 'export'])->name('daily.export');
            Route::get('daily/{bill}/invoice', [DailyBillingController::class, 'invoice'])->name('daily.invoice');
            Route::get('daily/{bill}/pdf', [DailyBillingController::class, 'downloadPdf'])->name('daily.pdf');
        });
        
        Route::middleware(['permission:create bills'])->group(function () {
            Route::post('day-load', [DayLoadBillingController::class, 'store'])->name('day-load.store');
            Route::post('weekly/bulk', [WeeklyBillingController::class, 'bulkStore'])->name('weekly.bulkStore');
            Route::post('weekly/purchase', [WeeklyBillingController::class, 'storePurchase'])->name('weekly.purchase.store');
            Route::post('weekly/generate', [WeeklyBillingController::class, 'generateWeekly'])->name('weekly.generate');
            Route::post('weekly/{bill}/pay-split/{part}', [WeeklyBillingController::class, 'paySplit'])->name('weekly.pay-split');
        });

        permissionResource('weekly', WeeklyBillingController::class, 'bills');
        permissionResource('daily', DailyBillingController::class, 'bills');
    });

    /*
    |--------------------------------------------------------------------------
    | Payments
    |--------------------------------------------------------------------------
    */
    Route::prefix('payments')->name('payments.')->group(function () {
        Route::middleware(['permission:view payments'])->group(function () {
            Route::get('customers/export', [CustomerPaymentController::class, 'export'])->name('customers.export');
            Route::get('dealers/export', [DealerPaymentController::class, 'export'])->name('dealers.export');
        });
        Route::middleware(['permission:view dealer ledger'])->group(function () {
            Route::get('dealers/{dealer}/ledger', [DealerPaymentController::class, 'ledger'])->name('dealers.ledger');
        });
        Route::middleware(['permission:view vendor payments'])->group(function () {
            Route::get('vendors/{vendor}/ledger', [\App\Http\Controllers\Payments\VendorPaymentController::class, 'ledger'])->name('vendors.ledger');
            Route::post('vendors/{vendor}/payments', [\App\Http\Controllers\Payments\VendorPaymentController::class, 'store'])->name('vendors.payments.store');
            Route::delete('vendors/{vendor}/payments/{payment}', [\App\Http\Controllers\Payments\VendorPaymentController::class, 'destroy'])->name('vendors.payments.destroy');
        });

        permissionResource('customers', CustomerPaymentController::class, 'payments');
        Route::get('dealers', fn () => redirect()->route('billing.weekly.index'))->name('dealers.index')->middleware('permission:view payments');
        Route::get('dealers/create', fn () => redirect()->route('billing.weekly.index'))->name('dealers.create')->middleware('permission:create payments');
        Route::post('dealers', [DealerPaymentController::class, 'store'])->name('dealers.store')->middleware('permission:create payments');
    });

    /*
    |--------------------------------------------------------------------------
    | Expenses & EMIs
    |--------------------------------------------------------------------------
    */
    Route::middleware(['permission:view expenses'])->group(function () {
        Route::get('expenses/categories', [ExpenseController::class, 'categories'])->name('expenses.categories');
        Route::get('expenses/export/csv', [ExpenseController::class, 'export'])->name('expenses.export');
    });
    Route::middleware(['permission:view emis'])->group(function () {
        Route::get('expenses/emis', [ExpenseController::class, 'emisIndex'])->name('expenses.emis.index');
        Route::get('expenses/emis/alerts', [ExpenseController::class, 'emisAlerts'])->name('expenses.emis.alerts');
    });
    Route::middleware(['permission:create emis'])->group(function () {
        Route::get('expenses/emis/create', [ExpenseController::class, 'emisCreate'])->name('expenses.emis.create');
        Route::post('expenses/emis', [ExpenseController::class, 'storeEmi'])->name('expenses.emis.store');
    });
    Route::middleware(['permission:delete emis'])->group(function () {
        Route::delete('expenses/emis/{emi}', [ExpenseController::class, 'destroyEmi'])->name('expenses.emis.destroy');
    });
    Route::middleware(['permission:edit emis'])->group(function () {
        Route::get('expenses/emis/{emi}/edit', [ExpenseController::class, 'emisEdit'])->name('expenses.emis.edit');
        Route::put('expenses/emis/{emi}', [ExpenseController::class, 'updateEmi'])->name('expenses.emis.update');
        Route::post('expenses/emis/{emi}/pay', [ExpenseController::class, 'payEmi'])->name('expenses.emis.pay');
        Route::post('expenses/emis/{emi}/close-full', [ExpenseController::class, 'closeFullEmi'])->name('expenses.emis.close-full');
    });

    permissionResource('expenses', ExpenseController::class, 'expenses');

    /*
    |--------------------------------------------------------------------------
    | Profit Analysis & Reports
    |--------------------------------------------------------------------------
    */
    Route::middleware(['permission:view profit dashboard'])->group(function () {
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
    });

    Route::middleware(['permission:view reports'])->group(function () {
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
    | Routes & Delivery
    |--------------------------------------------------------------------------
    */
    Route::prefix('routes')->name('routes.')->group(function () {
        Route::middleware(['permission:view routes'])->group(function () {
            Route::get('/', [RouteController::class, 'index'])->name('index');
        });
        Route::middleware(['permission:create routes'])->group(function () {
            Route::post('/', [RouteController::class, 'store'])->name('store');
        });
        Route::middleware(['permission:create vehicles'])->group(function () {
            Route::post('/vehicles', [RouteController::class, 'storeVehicle'])->name('vehicles.store');
        });
        Route::middleware(['permission:create drivers'])->group(function () {
            Route::post('/drivers', [RouteController::class, 'storeDriver'])->name('drivers.store');
        });
    });

    /*
    |--------------------------------------------------------------------------
    | Admin & User Management
    |--------------------------------------------------------------------------
    */
    Route::prefix('admin')->name('admin.')->group(function () {
        Route::middleware(['permission:edit users'])->group(function () {
            Route::post('users/{user}/toggle-status', [UserManagementController::class, 'toggleStatus'])->name('users.toggle-status');
        });
        permissionResource('users', UserManagementController::class, 'users');

        Route::middleware(['permission:view activity logs'])->group(function () {
            Route::get('activity-logs', [UserManagementController::class, 'activityLogs'])->name('activity-logs');
        });
        
        Route::middleware(['permission:manage roles'])->group(function () {
            Route::get('roles/{role}/assign-permissions', [RoleController::class, 'assignPermissionPage'])->name('roles.assignPermissionPage');
            Route::post('roles/assign-permissions', [RoleController::class, 'assignPermission'])->name('roles.assignPermission');
        });
        permissionResource('roles', RoleController::class, 'roles');
        permissionResource('permissions', PermissionController::class, 'permissions');
    });
});
