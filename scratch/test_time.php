<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$engine = app(\App\Services\SignalEngine::class);
$markets = \App\Models\Market::whereIn('symbol', ['XAUUSD', 'BTCUSD', 'EURUSD'])->get();

$totalStart = microtime(true);
foreach ($markets as $market) {
    echo "Scanning {$market->symbol}...\n";
    $start = microtime(true);
    
    // Step 1: Feed
    $step1 = microtime(true);
    $feed = app(\App\Services\MarketDataService::class);
    $candles = $feed->candles($market, 'H1', null, true);
    echo "  Feed: " . (microtime(true) - $step1) . "s\n";
    
    // Step 2: AI
    $step2 = microtime(true);
    $analysis = app(\App\Services\AiMarketAnalysisService::class);
    $ai = $analysis->analyze($market, $candles);
    echo "  AI: " . (microtime(true) - $step2) . "s\n";
    
    // Step 3: Strategies
    $step3 = microtime(true);
    $created = [];
    foreach (\App\Domain\Strategies\StrategyRegistry::all() as $strategy) {
        $result = $strategy->analyze($candles);
    }
    echo "  Strategies: " . (microtime(true) - $step3) . "s\n";

    // Step 4: Trendlines
    $step4 = microtime(true);
    $trendlines = app(\App\Services\TrendlineDetector::class);
    $trendlines->detect($market, $candles, 'H1');
    echo "  Trendlines: " . (microtime(true) - $step4) . "s\n";
    
    echo "  Total Market time: " . (microtime(true) - $start) . "s\n";
}

echo "TOTAL TIME: " . (microtime(true) - $totalStart) . "s\n";
