<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$date = '2026-08-10';
$dailyBills = \App\Models\DailyBill::with('items')->whereDate('date', $date)->whereNotNull('dealer_id')->where('status', '!=', 'Cancelled')->get();
$dealerAdjustments = [];
foreach ($dailyBills as $bill) {
    $dayLoadEntry = \App\Models\DayLoadEntry::where('dealer_id', $bill->dealer_id)
        ->whereHas('batch', function($q) use ($date) {
            $q->whereDate('billing_date', $date);
        })->first();
    $dealerRate = $dayLoadEntry ? (float) $dayLoadEntry->customer_rate : 0;
    if ($dealerRate > 0) {
        $qtySold = $bill->items->sum('quantity_kg');
        $adj = $qtySold * $dealerRate;
        if (!isset($dealerAdjustments[$bill->dealer_id])) {
            $dealerAdjustments[$bill->dealer_id] = [
                'total_theoretical' => 0,
            ];
        }
        $dealerAdjustments[$bill->dealer_id]['total_theoretical'] += $adj;
    }
}
print_r($dealerAdjustments);
