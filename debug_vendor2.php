<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$user = App\Models\User::first();
auth()->login($user);

// Share errors ViewErrorBag like StartSession middleware does
$view = view();
$view->share('errors', session()->get('errors', new Illuminate\Support\ViewErrorBag));

$vendor = App\Models\Vendor::find(1);

try {
    $ctrl = app(App\Http\Controllers\Masters\VendorController::class);
    $response = $ctrl->show($vendor);
    $html = $response->render();
    echo "SUCCESS: Vendor show rendered successfully! (HTML length: " . strlen($html) . ")\n";
} catch (Throwable $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo "FILE: " . $e->getFile() . ":" . $e->getLine() . "\n";
    if ($prev = $e->getPrevious()) {
        echo "PREV: " . $prev->getMessage() . "\n";
        echo "PREV FILE: " . $prev->getFile() . ":" . $prev->getLine() . "\n";
    }
}
