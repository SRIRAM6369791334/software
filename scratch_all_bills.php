<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$bills = App\Models\DailyBill::whereDate('date', '2026-08-10')->get();
foreach($bills as $b) {
    echo 'Bill ID: ' . $b->id . ' Cust ID: ' . $b->customer_id . ' Amount: ' . $b->net_amount . ' Status: ' . $b->status . "\n";
}
