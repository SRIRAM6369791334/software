<?php
require 'c:/xampp/htdocs/Poultry Management System/flockwise-biztrack-main/flockwise-biztrack-laravel/vendor/autoload.php';
$app = require_once 'c:/xampp/htdocs/Poultry Management System/flockwise-biztrack-main/flockwise-biztrack-laravel/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== FIXED PROFIT DATA ===\n\n";

$profitService = app(\App\Services\ProfitService::class);
$summary = $profitService->getSummary();
echo "--- MONTHLY SUMMARY ---\n";
echo "Total Revenue -> Rs " . number_format($summary['revenue'], 2) . "\n";
echo "Total Purchases / Vendor -> Rs " . number_format($summary['purchase'], 2) . "\n";
echo "Total Expenses -> Rs " . number_format($summary['expenses'], 2) . "\n";
echo "Net Profit -> Rs " . number_format($summary['profit'], 2) . "\n";

$pBreakdown = $profitService->getProfitBreakdown('2026-08-10', '2026-08-16');
echo "\n--- WEEKLY BREAKDOWN (10-16 Aug 2026) ---\n";
echo "Total Billed Amount (Sales) -> Rs " . number_format($pBreakdown['total_billed'], 2) . "\n";
echo "Dealer Paid (Cash Inflow) -> Rs " . number_format($pBreakdown['dealer_paid'], 2) . "\n";
echo "Total Vendor Cost -> Rs " . number_format($pBreakdown['vendor_cost'], 2) . "\n";
echo "Vendor Paid -> Rs " . number_format($pBreakdown['vendor_paid'], 2) . "\n";
echo "Total Expenses -> Rs " . number_format($pBreakdown['total_expenses'], 2) . "\n";
echo "Accrual Net Profit -> Rs " . number_format($pBreakdown['net_profit'], 2) . "\n";
echo "Realized Cash Profit -> Rs " . number_format($pBreakdown['cash_profit'], 2) . "\n";
