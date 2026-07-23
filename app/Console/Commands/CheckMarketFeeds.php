<?php

namespace App\Console\Commands;

use App\Models\Market;
use App\Services\MarketDataService;
use Illuminate\Console\Command;

class CheckMarketFeeds extends Command
{
    protected $signature = 'forex:feed-check {symbol?} {--fresh : Ignore 30-second cache}';
    protected $description = 'Test market providers and print the actual source/status/price.';

    public function handle(MarketDataService $data): int
    {
        $markets = $this->argument('symbol')
            ? Market::where('symbol', strtoupper($this->argument('symbol')))->get()
            : Market::orderBy('symbol')->get();
        if ($markets->isEmpty()) { $this->error('Market not found.'); return self::FAILURE; }

        $rows = [];
        foreach ($markets as $market) {
            $data->candles($market, 'H1', 100, $this->option('fresh'));
            $market->refresh();
            $rows[] = [$market->symbol, $market->price, strtoupper($market->data_status), $market->data_source, optional($market->price_fetched_at)->toDateTimeString(), $market->feed_error ?: '—'];
        }
        $this->table(['Symbol','Price','Status','Source','Fetched at','Error'], $rows);
        if ($markets->contains(fn($m) => $m->fresh()->data_status === 'demo')) {
            $this->warn('DEMO means remote feeds failed. Check internet/firewall or configure TWELVEDATA_API_KEY.');
        }
        return self::SUCCESS;
    }
}
