<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$market = \App\Models\Market::where('symbol', 'XAUUSD')->first();
$feed = app(\App\Services\MarketDataService::class);
$candles = $feed->candles($market, 'H1', null, true);
echo "Candles count: " . count($candles) . "\n";

$start = microtime(true);
$analysis = app(\App\Services\AiMarketAnalysisService::class);
$analysis->swings($candles);
echo "Swings time: " . (microtime(true) - $start) . "s\n";

$start2 = microtime(true);
$market->trendlines()->where('timeframe', 'H1')->delete();
echo "Delete time: " . (microtime(true) - $start2) . "s\n";
