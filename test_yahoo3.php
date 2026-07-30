<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$yahooResponse = Illuminate\Support\Facades\Http::get('https://query1.finance.yahoo.com/v8/finance/chart/GC=F?interval=15m&range=1d');
$result = $yahooResponse->json('chart.result.0');
$quote = $result['indicators']['quote'][0] ?? [];
echo "Yahoo Close: " . end($quote['close']) . "\n";
