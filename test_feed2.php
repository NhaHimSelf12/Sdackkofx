<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$market = App\Models\Market::where('symbol', 'XAUUSD')->first();

// I will re-implement the fetch logic here to see what exactly fails
$symbol = 'XAU/USD';
$response = Illuminate\Support\Facades\Http::timeout(12)->get('https://api.twelvedata.com/time_series', [
    'symbol'=>$symbol,'interval'=>'1h','outputsize'=>10,'apikey'=>config('forex.twelvedata_key'),
]);
echo "TwelveData Response: " . $response->body() . "\n";

$yahooResponse = Illuminate\Support\Facades\Http::get('https://query1.finance.yahoo.com/v8/finance/chart/GC=F?interval=60m&range=1mo');
echo "Yahoo Response Status: " . $yahooResponse->status() . "\n";
if (!$yahooResponse->successful()) {
    echo "Yahoo Error: " . $yahooResponse->body() . "\n";
}
