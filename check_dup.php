<?php
require __DIR__.'/vendor/autoload.php'; 
$app = require_once __DIR__.'/bootstrap/app.php'; 
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class); 
$kernel->bootstrap(); 

$purchases = \App\Models\Purchase::latest()->take(3)->get();
foreach($purchases as $p) {
    echo $p->id . ' - ' . $p->total_amount . ' - ' . $p->created_at . "\n";
}
