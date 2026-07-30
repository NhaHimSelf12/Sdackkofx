<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$market = App\Models\Market::where('symbol', 'XAUUSD')->first();
$service = app(App\Services\MarketDataService::class);
$candles = $service->candles($market, 'H1', 10, true);
$market->refresh();
echo "Last candle close: " . end($candles)['close'] . "\n";
echo "Feed Error: {$market->feed_error}\n";
echo "Data Source: {$market->data_source}\n";
