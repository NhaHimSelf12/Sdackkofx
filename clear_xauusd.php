<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

App\Models\Signal::where('market_id', App\Models\Market::where('symbol', 'XAUUSD')->value('id'))->update(['status' => 'expired']);
echo "Signals expired.\n";
