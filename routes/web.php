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
use App\Http\Controllers\CashBankLedgerController;
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
        Route::get('/purchases/invoices/export', [PurchaseController::class, 'invoicesExport'])->name('purchases.invoices.export');
        Route::get('/purchases/invoices/{date}/print', [PurchaseController::class, 'invoicesPrint'])->name('purchases.invoices.print');
        Route::get('/purchases/invoices/{date}/pdf', [PurchaseController::class, 'invoicesPdf'])->name('purchases.invoices.pdf');
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
            Route::get('day-load/export/csv', [DayLoadBillingController::class, 'export'])->name('day-load.export');
            Route::get('day-load/{date}/invoice', [DayLoadBillingController::class, 'invoice'])->name('day-load.invoice');
            Route::get('day-load/{date}/pdf', [DayLoadBillingController::class, 'downloadPdf'])->name('day-load.pdf');
            Route::get('weekly/bulk', [WeeklyBillingController::class, 'bulk'])->name('weekly.bulk');
            Route::get('weekly/dealer-invoice', [WeeklyBillingController::class, 'dealerInvoice'])->name('weekly.dealer-invoice');
            Route::post('weekly/dealer-invoice/generate', [WeeklyBillingController::class, 'generateInvoice'])->name('weekly.generate-invoice');
            Route::get('weekly/{weekly}/whatsapp', [WeeklyBillingController::class, 'whatsapp'])->name('weekly.whatsapp');
            Route::get('weekly/{weekly}/pdf', [WeeklyBillingController::class, 'downloadPdf'])->name('weekly.pdf');
            Route::get('weekly/export/csv', [WeeklyBillingController::class, 'export'])->name('weekly.export');
            Route::get('weekly/calculate-preview', [WeeklyBillingController::class, 'calculatePreview'])->name('weekly.calculate-preview');
            Route::get('weekly/earliest-unpaid-date', [WeeklyBillingController::class, 'getEarliestUnpaidDate'])->name('weekly.earliest-unpaid-date');
            
            Route::get('daily/gst/view', [DailyBillingController::class, 'gst'])->name('daily.gst');
            Route::get('daily/export/csv', [DailyBillingController::class, 'export'])->name('daily.export');
            Route::get('daily/calculate-preview', [DailyBillingController::class, 'calculatePreview'])->name('daily.calculate-preview');
            Route::get('daily/get-dealer-stock', [DailyBillingController::class, 'getDealerStock'])->name('daily.get-dealer-stock');
            Route::get('daily/{daily}/whatsapp', [DailyBillingController::class, 'whatsapp'])->name('daily.whatsapp');
            Route::get('daily/{bill}/invoice', [DailyBillingController::class, 'invoice'])->name('daily.invoice');
            Route::get('daily/{bill}/pdf', [DailyBillingController::class, 'downloadPdf'])->name('daily.pdf');
        });
        
        Route::middleware(['permission:create bills'])->group(function () {
            Route::post('day-load', [DayLoadBillingController::class, 'store'])->name('day-load.store');
            Route::post('day-load/{entry}/transfer', [DayLoadBillingController::class, 'transfer'])->name('day-load.transfer');
            Route::put('day-load/{entry}/update', [DayLoadBillingController::class, 'update'])->name('day-load.update');
            Route::put('day-load/bulk-update', [DayLoadBillingController::class, 'bulkUpdate'])->name('day-load.bulk-update');
            Route::post('day-load/set-farm-weight', [DayLoadBillingController::class, 'setFarmWeight'])->name('day-load.set-farm-weight');
            Route::post('day-load/{entry}/dealer-payment', [DayLoadBillingController::class, 'recordDealerPayment'])->name('day-load.dealer-payment');
            Route::post('day-load/{entry}/vendor-payment', [DayLoadBillingController::class, 'recordVendorPayment'])->name('day-load.vendor-payment');
            Route::post('day-load/lumpsum-dealer-payment', [DayLoadBillingController::class, 'recordLumpSumDealerPayment'])->name('day-load.lumpsum-dealer-payment');
            Route::get('day-load/vendor-rates', [DayLoadBillingController::class, 'vendorRatesForm'])->name('day-load.vendor-rates');
            Route::post('day-load/vendor-rates', [DayLoadBillingController::class, 'setVendorRates'])->name('day-load.set-vendor-rates');
            Route::post('day-load/batch/{batch}/approve-weight-loss', [DayLoadBillingController::class, 'approveWeightLoss'])->name('day-load.approve-weight-loss');

            Route::post('day-load/{entry}/apply-advance', [DayLoadBillingController::class, 'applyAdvance'])->name('day-load.apply-advance');
            Route::delete('day-load/advance-adjustments/{adjustment}', [DayLoadBillingController::class, 'removeAdvanceAdjustment'])->name('day-load.remove-advance-adjustment');

            // Cash & Bank Ledger (route names are placeholders; sidebar menu placement to be finalized by project owner)
            Route::get('cash-bank-ledger', [CashBankLedgerController::class, 'index'])->name('cash-bank-ledger.index');
            Route::get('cash-bank-ledger/{date}/details', [CashBankLedgerController::class, 'showDay'])->name('cash-bank-ledger.show-day');
            Route::post('cash-bank-ledger/{ledger}/approve', [CashBankLedgerController::class, 'approve'])->name('cash-bank-ledger.approve');

            // Capital Investments & Drawings
            Route::resource('investments', \App\Http\Controllers\CapitalTransactionController::class)->names('investments')->only(['index', 'store', 'destroy']);

            // Live Deploy Sync Route (For Servers Without Terminal Access)
            Route::get('live-deploy-sync-2026', function () {
                \Illuminate\Support\Facades\Artisan::call('migrate', ['--force' => true]);
                \Illuminate\Support\Facades\Artisan::call('view:clear');
                \Illuminate\Support\Facades\Artisan::call('cache:clear');
                \Illuminate\Support\Facades\Artisan::call('config:clear');

                // Sync approved_amount with cash_income for historical approved days if needed
                \App\Models\CashBankLedger::where('is_approved', true)->get()->each(function ($l) {
                    if ((float) $l->approved_amount < (float) $l->cash_income) {
                        $l->update(['approved_amount' => $l->cash_income]);
                    }
                });

                // Auto-allocate unallocated vendor payments to unpaid Day-Load entries
                $dayLoadService = app(\App\Services\DayLoadPaymentService::class);
                $unallocatedVendorPayments = \App\Models\VendorPayment::whereNull('day_load_entry_id')
                    ->where('amount', '>', 0)
                    ->orderBy('date')
                    ->orderBy('id')
                    ->get();

                foreach ($unallocatedVendorPayments as $payment) {
                    $vendorId = $payment->vendor_id;
                    $remaining = (float) $payment->amount;

                    $entries = \App\Models\DayLoadEntry::where('vendor_id', $vendorId)
                        ->where('status', '!=', 'Cancelled')
                        ->with('batch')
                        ->get()
                        ->sortBy(function($e) {
                            return $e->batch ? $e->batch->billing_date->timestamp : $e->created_at->timestamp;
                        });

                    foreach ($entries as $entry) {
                        $balance = round((float)$entry->vendor_cost - (float)$entry->vendor_paid, 2);
                        if ($balance <= 0) continue;

                        $alloc = min($remaining, $balance);
                        $entry->increment('vendor_paid', $alloc);
                        $dayLoadService->refreshVendorPaymentStatus($entry);
                        if ($entry->batch) {
                            $dayLoadService->refreshBatchFinancials($entry->batch);
                        }

                        $payment->update([
                            'day_load_entry_id' => $entry->id,
                            'amount'            => $alloc,
                            'notes'             => ($payment->notes ?: 'Vendor payment') . " (Allocated to entry #{$entry->id})",
                        ]);

                        $remaining = round($remaining - $alloc, 2);
                        if ($remaining <= 0) break;

                        if ($remaining > 0) {
                            $newUnallocated = $payment->replicate();
                            $newUnallocated->day_load_entry_id = null;
                            $newUnallocated->amount = $remaining;
                            $newUnallocated->cash_amount = $payment->amount > 0 ? round($remaining * ($payment->cash_amount / $payment->amount), 2) : 0;
                            $newUnallocated->bank_amount = round($remaining - $newUnallocated->cash_amount, 2);
                            $newUnallocated->save();
                            $payment = $newUnallocated;
                        }
                    }
                }

                // Force recalculate from earliest date forward
                $service = app(\App\Services\CashBankLedgerService::class);
                $ledgers = \App\Models\CashBankLedger::orderBy('ledger_date', 'asc')->get();
                foreach ($ledgers as $l) {
                    $service->recalculateForDate(\Carbon\Carbon::parse($l->ledger_date));
                }

                \Illuminate\Support\Facades\Artisan::call('ledger:audit', ['--fix' => true]);

                $june29 = \App\Models\CashBankLedger::whereDate('ledger_date', '2026-06-29')->first();

                return response()->json([
                    'status'                => 'success',
                    'version'               => 'v2.1-approved-sweep-fix',
                    'message'               => 'Live migration, view clear, and ledger audit fix completed successfully!',
                    'service_last_modified' => date('Y-m-d H:i:s', filemtime(app_path('Services/CashBankLedgerService.php'))),
                    'june_29_closing_bank'  => $june29 ? $june29->closing_bank_balance : 'N/A',
                    'june_29_record'        => $june29,
                ]);
            });

            Route::post('daily/generate', [DailyBillingController::class, 'generateDaily'])->name('daily.generate');
            Route::post('weekly/bulk', [WeeklyBillingController::class, 'bulkStore'])->name('weekly.bulkStore');
            Route::post('weekly/purchase', [WeeklyBillingController::class, 'storePurchase'])->name('weekly.purchase.store');
            Route::post('weekly/generate', [WeeklyBillingController::class, 'generateWeekly'])->name('weekly.generate');
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
            Route::get('vendors', [\App\Http\Controllers\Payments\VendorPaymentController::class, 'index'])->name('vendors.index');
            Route::get('vendors/create', [\App\Http\Controllers\Payments\VendorPaymentController::class, 'create'])->name('vendors.create');
            Route::post('vendors', [\App\Http\Controllers\Payments\VendorPaymentController::class, 'storeGeneralPayment'])->name('vendors.storeGeneralPayment');
            Route::get('vendors/export', [\App\Http\Controllers\Payments\VendorPaymentController::class, 'export'])->name('vendors.export');

            Route::post('vendors/advances', [\App\Http\Controllers\Payments\VendorPaymentController::class, 'storeAdvance'])->name('vendors.advances.store');
            Route::delete('vendors/advances/{advance}', [\App\Http\Controllers\Payments\VendorPaymentController::class, 'destroyAdvance'])->name('vendors.advances.destroy');
            Route::get('vendors/{vendor}/ledger', [\App\Http\Controllers\Payments\VendorPaymentController::class, 'ledger'])->name('vendors.ledger');
            Route::post('vendors/{vendor}/payments', [\App\Http\Controllers\Payments\VendorPaymentController::class, 'store'])->name('vendors.payments.store');
            Route::delete('vendors/{vendor}/payments/{payment}', [\App\Http\Controllers\Payments\VendorPaymentController::class, 'destroy'])->name('vendors.payments.destroy');
        });

        permissionResource('customers', CustomerPaymentController::class, 'payments');
        Route::get('dealers', [DealerPaymentController::class, 'index'])->name('dealers.index')->middleware('permission:view payments');
        Route::get('dealers/create', [DealerPaymentController::class, 'create'])->name('dealers.create')->middleware('permission:create payments');
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
            Route::get('/weekly', [ProfitController::class, 'weeklyDetail'])->name('weekly-detail');
            Route::get('/weekly-detail', [ProfitController::class, 'weeklyDetail'])->name('weekly-detail');
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

Route::get('/run-updates', function () {
    try {
        // 1. Run migrations (adds payment_group_id + bank_expense columns)
        \Illuminate\Support\Facades\Artisan::call('migrate', ['--force' => true]);
        echo "Database migrations run successfully!<br>";

        // 2. Clear caches
        \Illuminate\Support\Facades\Artisan::call('view:clear');
        \Illuminate\Support\Facades\Artisan::call('cache:clear');
        \Illuminate\Support\Facades\Artisan::call('route:clear');
        echo "Caches cleared successfully!<br>";

        // 3. Re-allocate historical general dealer payments using the new FIFO record method
        $dealerPayments = \App\Models\DealerPayment::whereNull('day_load_entry_id')->orderBy('date')->orderBy('id')->get();
        if ($dealerPayments->isNotEmpty()) {
            $paymentRecords = [];
            foreach ($dealerPayments as $p) {
                $paymentRecords[] = [
                    'dealer_id'          => $p->dealer_id,
                    'date'               => $p->date->format('Y-m-d'),
                    'payment_mode'       => $p->payment_mode,
                    'cash_amount'        => (float) $p->cash_amount,
                    'bank_amount'        => (float) $p->bank_amount,
                    'bank_transfer_type' => $p->bank_transfer_type,
                    'reference_number'   => $p->reference_number,
                    'notes'              => $p->notes,
                ];
                $p->delete();
            }

            // Reset collected on entries for affected dealers
            $dealerIds = array_unique(array_column($paymentRecords, 'dealer_id'));
            foreach ($dealerIds as $dealerId) {
                \App\Models\DayLoadEntry::where('dealer_id', $dealerId)->update([
                    'dealer_collected'      => 0.00,
                    'dealer_payment_status' => 'Pending'
                ]);
            }

            // Re-record via Service to trigger FIFO allocations
            $service = app(\App\Services\DealerPaymentService::class);
            foreach ($paymentRecords as $data) {
                $service->record($data);
            }
            echo "Re-allocated " . count($paymentRecords) . " historical dealer payments.<br>";
        }

        // 4. Recalculate Dealer Payments pending_balance_after
        $dealersUpdated = 0;
        foreach (\App\Models\Dealer::all() as $dealer) {
            $dayLoadsSum        = (float) \App\Models\DayLoadEntry::where('dealer_id', $dealer->id)->where('status', '!=', 'Cancelled')->get()->sum('amount');
            $directPaymentsSum  = (float) \App\Models\DealerPayment::where('dealer_id', $dealer->id)->whereNull('day_load_entry_id')->sum('amount');
            $initialBalance     = (float) $dealer->pending_amount + $directPaymentsSum + $dayLoadsSum;

            $payments = \App\Models\DealerPayment::where('dealer_id', $dealer->id)->orderBy('date')->orderBy('id')->get();

            $runningBalance = $initialBalance;
            foreach ($payments as $p) {
                $runningBalance = round($runningBalance - (float) $p->amount, 2);
                $p->updateQuietly(['pending_balance_after' => max(0, $runningBalance)]);
                $dealersUpdated++;
            }
        }
        echo "Dealer payments updated: {$dealersUpdated} records.<br>";

        // 5. Recalculate Vendor Payments pending_balance_after
        $vendorsUpdated = 0;
        foreach (\App\Models\Vendor::all() as $vendor) {
            $totalCreditPurchases    = (float) \App\Models\Purchase::where('vendor_id', $vendor->id)->where('payment_mode', 'Credit')->sum('total_amount');
            $totalDayLoadLiabilities = (float) \App\Models\DayLoadEntry::where('vendor_id', $vendor->id)->where('status', '!=', 'Cancelled')->get()->sum('vendor_cost');
            $initialBalance          = $totalCreditPurchases + $totalDayLoadLiabilities;

            $payments = \App\Models\VendorPayment::where('vendor_id', $vendor->id)->orderBy('date')->orderBy('id')->get();

            $runningBalance = $initialBalance;
            foreach ($payments as $p) {
                $runningBalance = round($runningBalance - (float) $p->amount, 2);
                $p->updateQuietly(['pending_balance_after' => max(0, $runningBalance)]);
                $vendorsUpdated++;
            }
        }
        echo "Vendor payments updated: {$vendorsUpdated} records.<br>";

        // 6. Recalculate all Cash/Bank Ledger rows (oldest first) to fix bank_expense + cascade balances
        $ledgerService  = app(\App\Services\CashBankLedgerService::class);
        $ledgerRows     = \App\Models\CashBankLedger::orderBy('ledger_date')->get();
        $ledgerCount    = 0;
        foreach ($ledgerRows as $row) {
            $ledgerService->recalculateForDate(\Carbon\Carbon::parse($row->ledger_date));
            $ledgerCount++;
        }
        echo "Cash/Bank Ledger recalculated: {$ledgerCount} rows.<br>";

        echo "<br><strong style='color:green'>All updates finished successfully!</strong>";
    } catch (\Exception $e) {
        echo "<strong style='color:red'>Error: " . $e->getMessage() . "</strong>";
    }
});

/*
|--------------------------------------------------------------------------
| Live Server Database Export / Download Route
|--------------------------------------------------------------------------
| Visit: /export-db-2026 in browser to download full database SQL dump
*/
Route::get('/export-db-2026', function () {
    try {
        $driver = \Illuminate\Support\Facades\DB::connection()->getDriverName();
        $dbName = \Illuminate\Support\Facades\DB::connection()->getDatabaseName();
        $filename = 'db_export_' . ($dbName ?: 'backup') . '_' . date('Y-m-d_H-i-s') . '.sql';

        return response()->streamDownload(function () use ($driver, $dbName) {
            $pdo = \Illuminate\Support\Facades\DB::connection()->getPdo();

            echo "-- ========================================================\n";
            echo "-- Database Export ({$driver})\n";
            echo "-- Database Name: {$dbName}\n";
            echo "-- Exported Date: " . date('Y-m-d H:i:s') . "\n";
            echo "-- ========================================================\n\n";

            if ($driver === 'sqlite') {
                echo "PRAGMA foreign_keys = OFF;\nBEGIN TRANSACTION;\n\n";

                $stmt = $pdo->query("SELECT name, sql FROM sqlite_master WHERE type='table' AND name NOT LIKE 'sqlite_%'");
                $tables = $stmt->fetchAll(\PDO::FETCH_ASSOC);

                foreach ($tables as $t) {
                    $tableName = $t['name'];
                    $createSql = $t['sql'];

                    echo "-- --------------------------------------------------------\n";
                    echo "-- Table structure for table \"{$tableName}\"\n";
                    echo "-- --------------------------------------------------------\n";
                    echo "DROP TABLE IF EXISTS \"{$tableName}\";\n";
                    echo $createSql . ";\n\n";

                    echo "-- Dumping data for table \"{$tableName}\"\n";
                    $dataStmt = $pdo->query("SELECT * FROM \"{$tableName}\"");
                    while ($row = $dataStmt->fetch(\PDO::FETCH_ASSOC)) {
                        $cols = array_map(fn($c) => "\"{$c}\"", array_keys($row));
                        $vals = array_map(fn($v) => is_null($v) ? 'NULL' : $pdo->quote($v), array_values($row));
                        echo "INSERT INTO \"{$tableName}\" (" . implode(', ', $cols) . ") VALUES (" . implode(', ', $vals) . ");\n";
                    }
                    echo "\n\n";
                    flush();
                }

                echo "COMMIT;\nPRAGMA foreign_keys = ON;\n";
            } else {
                // MySQL / MariaDB
                echo "SET FOREIGN_KEY_CHECKS=0;\n";
                echo "SET SQL_MODE = \"NO_AUTO_VALUE_ON_ZERO\";\n";
                echo "SET AUTOCOMMIT = 0;\n";
                echo "START TRANSACTION;\n\n";

                $stmt = $pdo->query("SHOW FULL TABLES WHERE Table_type = 'BASE TABLE'");
                $tables = [];
                while ($row = $stmt->fetch(\PDO::FETCH_NUM)) {
                    $tables[] = $row[0];
                }

                foreach ($tables as $table) {
                    echo "-- --------------------------------------------------------\n";
                    echo "-- Table structure for table `{$table}`\n";
                    echo "-- --------------------------------------------------------\n";
                    echo "DROP TABLE IF EXISTS `{$table}`;\n";

                    $createStmt = $pdo->query("SHOW CREATE TABLE `{$table}`");
                    $createRow = $createStmt->fetch(\PDO::FETCH_ASSOC);
                    $createSql = $createRow['Create Table'] ?? array_values($createRow)[1] ?? '';
                    echo $createSql . ";\n\n";

                    echo "-- Dumping data for table `{$table}`\n";
                    $dataStmt = $pdo->query("SELECT * FROM `{$table}`");

                    $batch = [];
                    $batchSize = 100;
                    $cols = [];

                    while ($row = $dataStmt->fetch(\PDO::FETCH_ASSOC)) {
                        if (empty($cols)) {
                            $cols = array_map(fn($c) => "`{$c}`", array_keys($row));
                        }
                        $vals = array_map(function ($val) use ($pdo) {
                            if (is_null($val)) return 'NULL';
                            return $pdo->quote($val);
                        }, array_values($row));

                        $batch[] = "(" . implode(", ", $vals) . ")";

                        if (count($batch) >= $batchSize) {
                            echo "INSERT INTO `{$table}` (" . implode(", ", $cols) . ") VALUES\n" . implode(",\n", $batch) . ";\n";
                            $batch = [];
                        }
                    }

                    if (!empty($batch)) {
                        echo "INSERT INTO `{$table}` (" . implode(", ", $cols) . ") VALUES\n" . implode(",\n", $batch) . ";\n";
                    }

                    echo "\n\n";
                    flush();
                }

                echo "COMMIT;\n";
                echo "SET FOREIGN_KEY_CHECKS=1;\n";
            }
        }, $filename, [
            'Content-Type' => 'application/sql',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    } catch (\Exception $e) {
        return response('Database Export Error: ' . $e->getMessage(), 500);
    }
});

/*
|--------------------------------------------------------------------------
| Import / Seed SQL Dump Route (Live Server & Local)
|--------------------------------------------------------------------------
| Visit: /import-sql-dump in browser to import public/poultry_db (11).sql into DB
*/
Route::get('/import-sql-dump', function () {
    try {
        set_time_limit(300);
        ini_set('memory_limit', '512M');

        $sqlFile = public_path('poultry_db (11).sql');
        if (!file_exists($sqlFile)) {
            $files = glob(public_path('*.sql'));
            if (empty($files)) {
                return response('No .sql files found in public/ directory! Please upload your .sql file to public/ on the server.', 404);
            }
            usort($files, fn($a, $b) => filemtime($b) - filemtime($a));
            $sqlFile = $files[0];
        }

        $sql = file_get_contents($sqlFile);
        if (empty($sql)) {
            return response('SQL file is empty!', 400);
        }

        $pdo = \Illuminate\Support\Facades\DB::connection()->getPdo();
        $driver = \Illuminate\Support\Facades\DB::connection()->getDriverName();

        if ($driver === 'mysql' || $driver === 'mariadb') {
            $pdo->exec("SET FOREIGN_KEY_CHECKS=0;");
            $pdo->exec("SET SQL_MODE = 'NO_AUTO_VALUE_ON_ZERO';");

            // Wipe / Drop existing tables first to perform a clean seed
            $tablesStmt = $pdo->query("SHOW FULL TABLES WHERE Table_type = 'BASE TABLE'");
            while ($row = $tablesStmt->fetch(\PDO::FETCH_NUM)) {
                $tableName = $row[0];
                $pdo->exec("DROP TABLE IF EXISTS `{$tableName}`;");
            }
            $viewsStmt = $pdo->query("SHOW FULL TABLES WHERE Table_type = 'VIEW'");
            while ($row = $viewsStmt->fetch(\PDO::FETCH_NUM)) {
                $viewName = $row[0];
                $pdo->exec("DROP VIEW IF EXISTS `{$viewName}`;");
            }
        } elseif ($driver === 'sqlite') {
            $pdo->exec("PRAGMA foreign_keys = OFF;");
            $tablesStmt = $pdo->query("SELECT name FROM sqlite_master WHERE type='table' AND name NOT LIKE 'sqlite_%'");
            while ($row = $tablesStmt->fetch(\PDO::FETCH_ASSOC)) {
                $tableName = $row['name'];
                $pdo->exec("DROP TABLE IF EXISTS \"{$tableName}\";");
            }
        }

        \Illuminate\Support\Facades\DB::unprepared($sql);


        if ($driver === 'mysql' || $driver === 'mariadb') {
            $pdo->exec("SET FOREIGN_KEY_CHECKS=1;");
        } elseif ($driver === 'sqlite') {
            $pdo->exec("PRAGMA foreign_keys = ON;");
        }

        return response()->json([
            'status'  => 'success',
            'message' => 'Live Database imported & seeded successfully!',
            'file'    => basename($sqlFile),
            'size'    => filesize($sqlFile) . ' bytes',
        ]);
    } catch (\Exception $e) {
        return response('Database Import Error: ' . $e->getMessage(), 500);
    }
});




