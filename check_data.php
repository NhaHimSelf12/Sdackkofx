<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$market = App\Models\Market::where('symbol', 'XAUUSD')->first();
$service = app(App\Services\MarketDataService::class);
$candles = $service->candles($market, 'H1', null, false);
$closes = array_column($candles, 'close');
$count = count($closes);

echo "Recent closes:\n";
for($i = $count - 6; $i < $count; $i++) {
    echo "[$i] " . $closes[$i] . "\n";
}

$price = end($closes);
$momentum = $price - $closes[$count - 6];
$shortMomentum = $price - $closes[$count - 3];
$atr = app(App\Domain\Strategies\TechnicalConfluenceStrategy::class)->atr($candles, 14);

echo "Price: $price\n";
echo "Momentum (6): $momentum\n";
echo "Short Momentum (3): $shortMomentum\n";
echo "-ATR*0.5: " . (-$atr * 0.5) . "\n";
