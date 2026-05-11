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
    });

    /*
    |--------------------------------------------------------------------------
    | Accountant & Admin
    |--------------------------------------------------------------------------
    */
    Route::middleware(['role:admin|accountant'])->group(function () {
        // Billing
        Route::prefix('billing')->name('billing.')->group(function () {
            Route::resource('weekly', WeeklyBillingController::class);
            Route::post('weekly/bulk', [WeeklyBillingController::class, 'bulkStore'])->name('weekly.bulkStore');
            Route::get('weekly/{bill}/whatsapp', [WeeklyBillingController::class, 'whatsapp'])->name('weekly.whatsapp');

            Route::resource('daily', DailyBillingController::class);
            Route::get('daily/gst/view', [DailyBillingController::class, 'gst'])->name('daily.gst');
            Route::get('daily/export/csv', [DailyBillingController::class, 'export'])->name('daily.export');

            Route::get('weekly/export/csv', [WeeklyBillingController::class, 'export'])->name('weekly.export');
        });

        // Payments
        Route::prefix('payments')->name('payments.')->group(function () {
            Route::resource('customers', CustomerPaymentController::class);
            Route::resource('dealers', DealerPaymentController::class);
        });

        // Expenses & Profit
        Route::resource('expenses', ExpenseController::class);
        Route::get('/profit', [ProfitController::class, 'index'])->name('profit.index');

        // Reports
        Route::get('/reports', [ReportController::class, 'index'])->name('reports');
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
