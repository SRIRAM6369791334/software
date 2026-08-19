<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Clear views
Artisan::call('view:clear');

$vendor = App\Models\Vendor::find(1);
if (!$vendor) { echo "Vendor #1 not found\n"; exit(1); }

// Replicate VendorController@show logic
try {
    $totalPurchaseAmount = $vendor->purchases()->sum('total_amount');
    $totalPurchaseCount = $vendor->purchases()->count();
    $lastPurchaseDate = $vendor->purchases()->latest('date')->first()?->date;
    $totalBoxesLoaded = $vendor->dayLoadEntries()->sum('no_of_boxes');
    $totalBirdWeight = $vendor->dayLoadEntries()->sum('bird_weight');
    $totalFarmWeight = $vendor->dayLoadEntries()->sum('farm_weight');
    $totalLossWeight = $vendor->dayLoadEntries()->sum('loss_weight');
    $avgRateVariance = 0;
    $loadCount = $vendor->dayLoadEntries()->count();
    $totalCreditPurchases = (float) $vendor->purchases()->where('payment_mode', 'Credit')->sum('total_amount');
    $totalDayLoadLiabilities = 0;
    $totalPaymentsPaid = (float) $vendor->vendorPayments()->sum('amount');
    $outstandingBalance = 0;
    $advances = $vendor->advances()->with('adjustments.dayLoadEntry')->latest('date')->get();
    $totalAdvanceGiven = (float) $vendor->advances()->sum('total_amount');
    $totalAdvanceAdjusted = (float) $vendor->advances()->sum('adjusted_amount');
    $totalActiveAdvanceBalance = (float) $vendor->active_advance_balance;
    $netSettlementBalance = 0;
    
    $ledgerService = app(App\Services\CashBankLedgerService::class);
    $todayLedger = $ledgerService->getOrCreateForDate(now());
    $currentCashBalance = (float) $todayLedger->closing_cash_balance;
    $currentBankBalance = (float) $todayLedger->closing_bank_balance;
    $currentInvestmentBalance = App\Models\CapitalTransaction::getCurrentBalance();
    
    echo "Controller data OK\n";
    echo "Cash: $currentCashBalance, Bank: $currentBankBalance, Pool: $currentInvestmentBalance\n";
    
    // Now try rendering the view
    $html = view('masters.vendors.show', compact(
        'vendor',
        'totalPurchaseAmount', 'totalPurchaseCount', 'lastPurchaseDate',
        'totalBoxesLoaded', 'totalBirdWeight', 'totalFarmWeight', 'totalLossWeight',
        'avgRateVariance', 'loadCount',
        'totalCreditPurchases', 'totalDayLoadLiabilities', 'totalPaymentsPaid', 'outstandingBalance',
        'advances', 'totalAdvanceGiven', 'totalAdvanceAdjusted', 'totalActiveAdvanceBalance', 'netSettlementBalance',
        'currentCashBalance', 'currentBankBalance', 'currentInvestmentBalance'
    ))->render();
    
    echo "VIEW RENDER OK - length: " . strlen($html) . "\n";
} catch (Throwable $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo "FILE: " . $e->getFile() . ":" . $e->getLine() . "\n";
    
    // Check if it's a view error with previous exception
    if ($prev = $e->getPrevious()) {
        echo "PREVIOUS: " . $prev->getMessage() . "\n";
        echo "PREV FILE: " . $prev->getFile() . ":" . $prev->getLine() . "\n";
    }
}
