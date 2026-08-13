<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$dateStr = '2026-08-10';
$dailyBills = \App\Models\DailyBill::with('items')->whereDate('date', $dateStr)->whereNotNull('dealer_id')->where('status', '!=', 'Cancelled')->get();
$totalQty = 0;
foreach ($dailyBills as $bill) {
    echo 'Bill ID: ' . $bill->id . ' Qty: ' . $bill->items->sum('quantity_kg') . "\n";
    $totalQty += $bill->items->sum('quantity_kg');
}
echo 'Total Qty: ' . $totalQty . "\n";
