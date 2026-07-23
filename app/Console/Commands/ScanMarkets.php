<?php

namespace App\Console\Commands;

use App\Models\Market;
use App\Services\NewsAnalysisService;
use App\Services\SignalEngine;
use Illuminate\Console\Command;

class ScanMarkets extends Command
{
    protected $signature = 'forex:scan {symbol? : Scan a single symbol, e.g. XAUUSD}';

    protected $description = 'Run AI analysis, strategy signals and trendline detection for all markets (and refresh news).';

    public function handle(SignalEngine $engine, NewsAnalysisService $news): int
    {
        $markets = $this->argument('symbol')
            ? Market::where('symbol', strtoupper($this->argument('symbol')))->get()
            : Market::all();

        if ($markets->isEmpty()) {
            $this->error('No markets found. Run: php artisan db:seed');

            return self::FAILURE;
        }

        foreach ($markets as $market) {
            $signals = $engine->scan($market);
            $this->info(sprintf(
                '%s  bias=%s (%d%%)  signals=%d',
                str_pad($market->symbol, 8),
                $market->ai_bias,
                $market->ai_confidence,
                count($signals),
            ));
        }

        $fetched = $news->refresh();
        $this->info($fetched > 0 ? "News refreshed: {$fetched} articles." : 'News: using seeded/demo articles (no NEWSAPI_KEY).');

        return self::SUCCESS;
    }
}
