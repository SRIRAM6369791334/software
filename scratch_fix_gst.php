<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$bills = App\Models\DailyBill::where('customer_id', 4)->get();
foreach($bills as $b) {
    if ($b->gst_percentage > 0) {
        $b->gst_percentage = 0;
        $b->gst_amount = 0;
        $b->net_amount = $b->amount;
        $b->save();
        
        foreach($b->items as $item) {
            $item->tax_amount = 0;
            $item->total_amount = $item->quantity_kg * $item->rate_per_kg;
            $item->save();
        }
    }
}

$c = App\Models\Customer::find(4);
$c->balance = App\Models\DailyBill::where('customer_id', 4)->sum('net_amount');
$c->save();

echo "Fixed DB! New Balance: " . $c->balance . "\n";
