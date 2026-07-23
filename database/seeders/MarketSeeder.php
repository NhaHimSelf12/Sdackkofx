<?php

namespace Database\Seeders;

use App\Models\Market;
use App\Services\SignalEngine;
use Illuminate\Database\Seeder;

class MarketSeeder extends Seeder
{
    public function run(): void
    {
        $markets = [
            ['symbol' => 'XAUUSD', 'name' => 'Gold / US Dollar', 'category' => 'metals', 'price' => 2465.30],
            ['symbol' => 'XAGUSD', 'name' => 'Silver / US Dollar', 'category' => 'metals', 'price' => 31.42],
            ['symbol' => 'BTCUSD', 'name' => 'Bitcoin / US Dollar', 'category' => 'crypto', 'price' => 64850.00],
            ['symbol' => 'ETHUSD', 'name' => 'Ethereum / US Dollar', 'category' => 'crypto', 'price' => 3412.50],
            ['symbol' => 'EURUSD', 'name' => 'Euro / US Dollar', 'category' => 'forex', 'price' => 1.08542],
            ['symbol' => 'GBPUSD', 'name' => 'British Pound / US Dollar', 'category' => 'forex', 'price' => 1.27315],
            ['symbol' => 'USDJPY', 'name' => 'US Dollar / Japanese Yen', 'category' => 'forex', 'price' => 155.842],
            ['symbol' => 'AUDUSD', 'name' => 'Australian Dollar / US Dollar', 'category' => 'forex', 'price' => 0.66421],
            ['symbol' => 'USDCAD', 'name' => 'US Dollar / Canadian Dollar', 'category' => 'forex', 'price' => 1.37205],
            ['symbol' => 'US30', 'name' => 'Dow Jones 30', 'category' => 'indices', 'price' => 40125.00],
        ];

        foreach ($markets as $data) {
            Market::updateOrCreate(['symbol' => $data['symbol']], $data);
        }

        // Generate initial AI analysis, signals and trendlines for every market
        $engine = app(SignalEngine::class);
        Market::all()->each(fn (Market $market) => $engine->scan($market));
    }
}
