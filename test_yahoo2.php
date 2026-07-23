<?php
require __DIR__.'/vendor/autoload.php';
use Illuminate\Support\Facades\Http;
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$url = 'https://query1.finance.yahoo.com/v8/finance/chart/GC=F?interval=15m&range=1d';
$response = Http::get($url);
$data = $response->json();
$quotes = $data['chart']['result'][0]['indicators']['quote'][0] ?? [];
if(!empty($quotes['close'])) {
    echo "Latest GC=F closes:\n";
    $closes = array_filter($quotes['close']);
    $recent = array_slice($closes, -5);
    foreach($recent as $c) echo $c . "\n";
} else {
    echo "No data found for GC=F";
}
