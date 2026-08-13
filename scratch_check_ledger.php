<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$ledger = App\Models\CashBankLedger::whereDate('date', '2026-08-10')->first();
echo "Cash Income: " . $ledger->cash_income . "\n";
echo "Bank Income: " . $ledger->bank_income . "\n";
