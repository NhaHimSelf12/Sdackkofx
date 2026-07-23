<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$market = App\Models\Market::where('symbol', 'XAUUSD')->first();
$service = app(App\Services\MarketDataService::class);
$candles = $service->candles($market, 'H1', null, false);
$closes = array_column($candles, 'close');

$price = end($closes);
$momentum = $price - $closes[count($closes) - 6];
$shortMomentum = $price - $closes[count($closes) - 3];

$strategy = app(App\Domain\Strategies\TechnicalConfluenceStrategy::class);
// Use reflection to access protected atr method
$reflection = new ReflectionClass(get_class($strategy));
$method = $reflection->getMethod('atr');
$method->setAccessible(true);
$atr = $method->invokeArgs($strategy, [$candles, 14]);

echo "Price: $price\n";
echo "Momentum (6): $momentum\n";
echo "Short Momentum (3): $shortMomentum\n";
echo "ATR: $atr\n";
echo "ATR * 0.5: " . ($atr * 0.5) . "\n";
echo "-ATR * 0.5: " . (-$atr * 0.5) . "\n";
