<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$market = App\Models\Market::where('symbol', 'XAUUSD')->first();
if ($market) {
    echo "AI Bias: " . $market->ai_bias . "\n";
    $signal = App\Models\Signal::where('market_id', $market->id)->latest('id')->first();
    if ($signal) {
        echo "Strategy: " . $signal->strategy . "\n";
        echo "Direction: " . $signal->direction . "\n";
        echo "Entry: " . $signal->entry . "\n";
        echo "Price: " . $signal->feed_price . "\n";
        echo "Note: " . $signal->note . "\n";
        echo "Generated At: " . $signal->generated_at . "\n";
    } else {
        echo "No signals found for XAUUSD.\n";
    }
} else {
    echo "XAUUSD not found.\n";
}
