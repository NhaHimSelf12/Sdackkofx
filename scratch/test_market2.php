<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$market = App\Models\Market::where('symbol', 'XAUUSD')->first();
$dataService = new App\Services\MarketDataService();
$dataService->candles($market, 'M1', 2, true);
echo "Market status: {$market->data_status}\n";
echo "Market error: {$market->feed_error}\n";
