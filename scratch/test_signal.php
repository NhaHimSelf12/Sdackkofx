<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$engine = app(\App\Services\SignalEngine::class);
$market = \App\Models\Market::where('symbol', 'XAUUSD')->first();

echo "Market Symbol: " . $market->symbol . "\n";
echo "StrToUpper Match: " . (strtoupper($market->symbol) === 'XAUUSD' ? 'YES' : 'NO') . "\n";

$signals = $engine->scan($market);
foreach ($signals as $sig) {
    echo "Entry: {$sig->entry} | SL: {$sig->stop_loss} | TP: {$sig->take_profit}\n";
    echo "Risk/Reward: {$sig->risk_reward}\n";
}
