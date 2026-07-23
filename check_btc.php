<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$market = App\Models\Market::where('symbol', 'BTCUSD')->first();
if ($market) {
    echo "Market ID: " . $market->id . "\n";
    echo "Data Source: " . $market->data_source . "\n";
    $signal = App\Models\Signal::where('market_id', $market->id)->latest('id')->first();
    if ($signal) {
        echo "Strategy: " . $signal->strategy . "\n";
        echo "Direction: " . $signal->direction . "\n";
        echo "Entry: " . $signal->entry . "\n";
        echo "Price: " . $signal->feed_price . "\n";
        echo "Note: " . $signal->note . "\n";
        echo "Generated: " . $signal->generated_at . "\n";
    } else {
        echo "No signals found for BTCUSD.\n";
    }
} else {
    echo "BTCUSD not found.\n";
}
