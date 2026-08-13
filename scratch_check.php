<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$c = App\Models\Customer::find(4);
echo "Balance: " . $c->balance . "\n";
$bills = App\Models\DailyBill::where('customer_id', 4)->get();
foreach($bills as $b) {
    echo "Bill ID: " . $b->id . " Amount: " . $b->net_amount . " Status: " . $b->status . "\n";
}
