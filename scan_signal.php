<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$market = App\Models\Market::where('symbol', 'XAUUSD')->first();
$engine = app(App\Services\SignalEngine::class);
$engine->scan($market, 'H1', true);

$signal = App\Models\Signal::where('market_id', $market->id)->latest('id')->first();
if ($signal) {
    echo "AI Bias: " . $market->fresh()->ai_bias . "\n";
    echo "Strategy: " . $signal->strategy . "\n";
    echo "Direction: " . $signal->direction . "\n";
    echo "Entry: " . $signal->entry . "\n";
    echo "Price: " . $signal->feed_price . "\n";
    echo "Note: " . $signal->note . "\n";
} else {
    echo "No signals found for XAUUSD after scan.\n";
}
