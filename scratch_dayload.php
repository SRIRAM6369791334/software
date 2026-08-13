<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$entries = App\Models\DayLoadEntry::whereDate('created_at', '2026-08-10')->get();
foreach($entries as $e) {
    echo 'Entry ID: ' . $e->id . ' CustRate: ' . $e->customer_rate . ' BillRate: ' . $e->billing_rate . "\n";
}
