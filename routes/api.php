<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CustomerController;
use App\Http\Controllers\Api\DailyBillingController;
use App\Http\Controllers\Api\DealerController;
use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\Api\ProfitController;
use App\Http\Controllers\Api\PurchaseController;
use App\Http\Controllers\Api\ReportController;
use App\Http\Controllers\Api\VendorController;
use App\Http\Controllers\Api\WeeklyBillingController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\ExpenseController;
use App\Http\Controllers\Api\RouteController;
use App\Http\Controllers\Api\BirdBatchController;
use App\Http\Controllers\Api\ItemController;
use App\Http\Controllers\Api\WarehouseController;
use App\Http\Controllers\Api\BatchController;
use App\Http\Controllers\Api\ConsumptionController;
use App\Http\Controllers\Api\MortalityController;
use App\Http\Controllers\Api\StockController;
use App\Http\Controllers\Api\InventoryAnalyticsController;
use App\Http\Controllers\Api\UserManagementController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes - Version 1 (V1)
|--------------------------------------------------------------------------
*/

Route::prefix('v1')->as('api.v1.')->group(function () {

    // Public Routes (no auth required)
    Route::get('docs', function () {
        return response('<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Poultry Management V1 API Docs</title>
    <link rel="stylesheet" href="https://unpkg.com/swagger-ui-dist@5.9.0/swagger-ui.css" />
    <style>
        body { margin: 0; background: #0f172a; }
        .swagger-ui { filter: invert(88%) hue-rotate(180deg); }
        .swagger-ui .topbar { display: none; }
    </style>
</head>
<body>
    <div id="swagger-ui"></div>
    <script src="https://unpkg.com/swagger-ui-dist@5.9.0/swagger-ui-bundle.js" charset="UTF-8"></script>
    <script src="https://unpkg.com/swagger-ui-dist@5.9.0/swagger-ui-standalone-preset.js" charset="UTF-8"></script>
    <script>
        window.onload = () => {
            window.ui = SwaggerUIBundle({
                url: "/docs/openapi.yaml",
                dom_id: "#swagger-ui",
                presets: [
                    SwaggerUIBundle.presets.apis,
                    SwaggerUIStandalonePreset
                ],
                layout: "BaseLayout",
                deepLinking: true,
                showExtensions: true,
                filter: true
            });
        };
    </script>
</body>
</html>', 200, ['Content-Type' => 'text/html']);
    });

    Route::post('auth/login', [AuthController::class, 'login']);

    // Protected Routes (Sanctum + Permission Middleware)
    Route::middleware('auth:sanctum')->group(function () {

        // Session/Auth — no extra permission needed
        Route::post('auth/logout', [AuthController::class, 'logout']);
        Route::get('auth/profile', [AuthController::class, 'profile']);

        // Dashboard & Alerts — view dashboard
        Route::middleware('permission:view analytics')->group(function () {
            Route::get('dashboard', [DashboardController::class, 'index']);
            Route::get('dashboard/alerts', [DashboardController::class, 'alerts']);
        });

        // Customers — view/create/edit/delete
        Route::middleware('permission:view customers|create customers|edit customers|delete customers')->group(function () {
            Route::apiResource('masters/customers', CustomerController::class);
            Route::get('masters/customers/{customer}/billing-history', [CustomerController::class, 'billingHistory']);
            Route::get('masters/customers/{customer}/payment-history', [CustomerController::class, 'paymentHistory']);
        });

        // Dealers
        Route::middleware('permission:view dealers|create dealers|edit dealers|delete dealers')->group(function () {
            Route::apiResource('masters/dealers', DealerController::class);
            Route::get('masters/dealers/{dealer}/purchase-history', [DealerController::class, 'purchaseHistory']);
        });

        // Vendors
        Route::middleware('permission:view vendors|create vendors|edit vendors|delete vendors')->group(function () {
            Route::apiResource('masters/vendors', VendorController::class);
            Route::get('masters/vendors/{vendor}/purchase-history', [VendorController::class, 'purchaseHistory']);
        });

        // Routes & Warehouses
        Route::middleware('permission:manage routes|view routes')->group(function () {
            Route::apiResource('masters/routes', RouteController::class);
            Route::apiResource('masters/warehouses', WarehouseController::class);
        });

        // Billing — Daily & Weekly
        Route::middleware('permission:view bills|create bills|edit bills|delete bills')->group(function () {
            Route::apiResource('billing/daily', DailyBillingController::class)->except(['update']);
            Route::post('billing/weekly/bulk', [WeeklyBillingController::class, 'bulkStore']);
            Route::get('billing/weekly/{weekly_bill}/share-url', [WeeklyBillingController::class, 'shareUrl']);
            Route::apiResource('billing/weekly', WeeklyBillingController::class)->except(['update']);
        });

        // Purchases
        Route::middleware('permission:view purchases|create purchases|edit purchases|delete purchases')->group(function () {
            Route::apiResource('purchases', PurchaseController::class);
        });

        // Payments
        Route::middleware('permission:view payments|create payments|edit payments|delete payments')->group(function () {
            Route::get('payments/customers', [PaymentController::class, 'indexCustomers']);
            Route::post('payments/customers', [PaymentController::class, 'storeCustomerPayment']);
            Route::get('payments/dealers', [PaymentController::class, 'indexDealers']);
            Route::post('payments/dealers', [PaymentController::class, 'storeDealerPayment']);
            Route::get('payments/dealers/{dealer}/ledger', [PaymentController::class, 'dealerLedger']);
        });

        // Expenses & EMIs
        Route::middleware('permission:view expenses|create expenses|edit expenses|delete expenses|view emis|create emis|edit emis|delete emis')->group(function () {
            Route::get('expenses/categories', [ExpenseController::class, 'categories']);
            Route::get('expenses/emis', [ExpenseController::class, 'emisIndex']);
            Route::post('expenses/emis', [ExpenseController::class, 'storeEmi']);
            Route::delete('expenses/emis/{emi}', [ExpenseController::class, 'destroyEmi']);
            Route::get('expenses/alerts', [ExpenseController::class, 'emisAlerts']);
            Route::apiResource('expenses', ExpenseController::class);
        });

        // Livestock Cycle & Mortality
        Route::middleware('permission:view stock|create stock|edit stock|delete stock')->group(function () {
            Route::apiResource('batches', BatchController::class);
            Route::post('bird-batches/{batch}/mortality', [BirdBatchController::class, 'recordMortality']);
            Route::apiResource('bird-batches', BirdBatchController::class);
            Route::apiResource('mortalities', MortalityController::class);
        });

        // Inventory — Items & Stock
        Route::middleware('permission:view stock|create stock|edit stock|delete stock')->group(function () {
            Route::apiResource('items', ItemController::class);
            Route::apiResource('consumptions', ConsumptionController::class);
            Route::get('stock', [StockController::class, 'index']);
            Route::get('stock/movements', [StockController::class, 'movements']);
            Route::post('stock/adjust', [StockController::class, 'adjust']);
            Route::get('inventory/analytics', [InventoryAnalyticsController::class, 'index']);
        });

        // Financial / Profit
        Route::middleware('permission:view reports|view analytics')->group(function () {
            Route::get('profit', [ProfitController::class, 'index']);
            Route::get('profit/monthly', [ProfitController::class, 'monthly']);
            Route::get('profit/expense-vs-income', [ProfitController::class, 'expenseVsIncome']);
        });

        // Reports
        Route::middleware('permission:view reports')->group(function () {
            Route::get('reports', [ReportController::class, 'index']);
            Route::get('reports/sales/daily', [ReportController::class, 'salesDaily']);
            Route::get('reports/sales/weekly', [ReportController::class, 'salesWeekly']);
            Route::get('reports/sales/monthly', [ReportController::class, 'salesMonthly']);
            Route::get('reports/purchases/daily', [ReportController::class, 'purchasesDaily']);
            Route::get('reports/purchases/weekly', [ReportController::class, 'purchasesWeekly']);
            Route::get('reports/purchases/monthly', [ReportController::class, 'purchasesMonthly']);
            Route::get('reports/purchases/vendor-analytics', [ReportController::class, 'vendorAnalytics']);
            Route::get('reports/customers/ranking', [ReportController::class, 'customerRanking']);
            Route::get('reports/purchases/analytics', [ReportController::class, 'purchaseAnalytics']);
        });

        // User Management (Admin only)
        Route::middleware('permission:manage users')->group(function () {
            Route::post('users/{user}/toggle-status', [UserManagementController::class, 'toggleStatus']);
            Route::apiResource('users', UserManagementController::class);
        });

    });
});
