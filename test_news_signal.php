<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Add a test news item
\App\Models\NewsItem::create([
    'title' => 'Federal Reserve announces unexpected interest rate hike affecting Gold and Bitcoin',
    'source' => 'Test News',
    'url' => 'http://test.com',
    'published_at' => now(),
    'summary' => 'This is a test summary.',
    'sentiment' => 'bearish',
    'impact' => 'high',
    'symbols' => ['XAUUSD', 'BTCUSD']
]);

// scan
$market = App\Models\Market::where('symbol', 'BTCUSD')->first();
if($market) {
    app(App\Services\SignalEngine::class)->scan($market, 'H1', true);
    $signal = App\Models\Signal::where('market_id', $market->id)->latest('id')->first();
    if ($signal) {
        echo "Note: " . $signal->note . "\n";
        echo "Confidence: " . $signal->confidence . "\n";
    }
}
