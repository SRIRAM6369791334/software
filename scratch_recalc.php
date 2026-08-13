<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

app(\App\Services\CashBankLedgerService::class)->recalculateForDate(\Carbon\Carbon::parse('2026-08-10'));
echo "Recalculated";
