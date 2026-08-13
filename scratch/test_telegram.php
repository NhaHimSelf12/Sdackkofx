<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$telegram = app(\App\Services\TelegramService::class);

// Get latest signals for XAUUSD
$market = \App\Models\Market::where('symbol', 'XAUUSD')->first();
$signals = \App\Models\Signal::where('market_id', $market->id)
    ->where('status', 'active')
    ->latest()
    ->take(2)
    ->get();

echo "Found " . $signals->count() . " signals\n";

if ($signals->isNotEmpty()) {
    $result = $telegram->sendSignals($signals);
    echo "Telegram send result: " . ($result ? 'SUCCESS' : 'FAILED') . "\n";
} else {
    echo "No active signals to send\n";
}
