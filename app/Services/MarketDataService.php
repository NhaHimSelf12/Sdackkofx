<?php

namespace App\Services;

use App\Models\Market;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Market candles with honest feed status.
 *
 * Provider order:
 * 1. Twelve Data when TWELVEDATA_API_KEY is configured.
 * 2. Yahoo Finance public chart feed (no key; usually delayed).
 * 3. Deterministic demo candles ONLY when all remote feeds fail.
 *
 * The selected source/status/error is persisted on the Market model so the UI
 * never labels demo prices as live data.
 */
class MarketDataService
{
    private const YAHOO_SYMBOLS = [
        'XAUUSD' => 'GC=F',     // COMEX Gold futures: transparent spot proxy
        'XAGUSD' => 'SI=F',     // COMEX Silver futures
        'BTCUSD' => 'BTC-USD',
        'ETHUSD' => 'ETH-USD',
        'EURUSD' => 'EURUSD=X',
        'GBPUSD' => 'GBPUSD=X',
        'USDJPY' => 'JPY=X',
        'AUDUSD' => 'AUDUSD=X',
        'USDCAD' => 'CAD=X',
        'US30' => '^DJI',
    ];

    /** @return array<int, array{time:int,open:float,high:float,low:float,close:float}> */
    public function candles(Market $market, string $timeframe = 'H1', ?int $count = null, bool $fresh = false): array
    {
        $count ??= (int) config('forex.candle_lookback', 300);
        $key = "market-feed.v4.{$market->symbol}.{$timeframe}.{$count}";
        if ($fresh) Cache::forget($key);

        $ttl = in_array($timeframe, ['M1','M5']) ? 5 : 30;
        return Cache::remember($key, now()->addSeconds($ttl), function () use ($market, $timeframe, $count) {
            $errors = [];

            if (config('forex.twelvedata_key')) {
                try {
                    $candles = $this->fromTwelveData($market, $timeframe, $count);
                    if (count($candles) >= 10) {
                        $this->recordFeed($market, 'twelvedata', 'live', $candles);
                        return $candles;
                    }
                    $errors[] = 'Twelve Data returned insufficient candles.';
                } catch (\Throwable $e) {
                    $errors[] = 'Twelve Data: '.$e->getMessage();
                }
            }

            try {
                $candles = $this->fromYahoo($market, $timeframe, $count);
                if (count($candles) >= 10) {
                    // Yahoo public quotes can be delayed; never claim real-time.
                    $this->recordFeed($market, 'yahoo', 'delayed', $candles);
                    return $candles;
                }
                $errors[] = 'Yahoo returned insufficient candles.';
            } catch (\Throwable $e) {
                $errors[] = 'Yahoo: '.$e->getMessage();
            }

            $message = implode(' | ', $errors) ?: 'No remote market provider available.';
            Log::warning("{$market->symbol} using DEMO feed", ['error' => $message]);
            $candles = $this->demoCandles($market, $timeframe, $count);
            $this->recordFeed($market, 'demo', 'demo', $candles, $message);
            return $candles;
        });
    }

    private function fromYahoo(Market $market, string $timeframe, int $count): array
    {
        $symbol = self::YAHOO_SYMBOLS[$market->symbol] ?? null;
        if (!$symbol) throw new \RuntimeException("No Yahoo symbol mapping for {$market->symbol}");

        $interval = match ($timeframe) {
            'M1' => '1m',
            'M5' => '5m',
            'M15' => '15m',
            'D1' => '1d',
            default => '60m', // H1 and H4 (H4 is aggregated below)
        };
        $range = match ($timeframe) {
            'M1' => '1d',
            'M5', 'M15' => '5d',
            'D1' => '1y',
            default => '1mo',
        };

        $url = 'https://query1.finance.yahoo.com/v8/finance/chart/'.rawurlencode($symbol);
        $response = Http::withHeaders([
            'User-Agent' => 'Mozilla/5.0 FXCommand/2.1',
            'Accept' => 'application/json',
        ])->timeout(12)->retry(2, 250)->get($url, [
            'interval' => $interval, 'range' => $range,
            'includePrePost' => 'false', 'events' => 'div,splits',
        ]);

        if (!$response->successful()) throw new \RuntimeException('HTTP '.$response->status());
        $result = $response->json('chart.result.0');
        if (!$result) throw new \RuntimeException($response->json('chart.error.description', 'Invalid response'));

        $timestamps = $result['timestamp'] ?? [];
        $quote = $result['indicators']['quote'][0] ?? [];
        $candles = [];
        foreach ($timestamps as $i => $time) {
            $open = $quote['open'][$i] ?? null; $high = $quote['high'][$i] ?? null;
            $low = $quote['low'][$i] ?? null; $close = $quote['close'][$i] ?? null;
            $volume = $quote['volume'][$i] ?? 0;
            if ($open === null || $high === null || $low === null || $close === null) continue;
            $candles[] = ['time'=>(int)$time,'open'=>(float)$open,'high'=>(float)$high,'low'=>(float)$low,'close'=>(float)$close,'volume'=>(float)$volume];
        }

        if ($timeframe === 'H4') $candles = $this->aggregate($candles, 4 * 3600);
        return array_slice($candles, -$count);
    }

    private function aggregate(array $candles, int $bucketSeconds): array
    {
        $buckets = [];
        foreach ($candles as $c) {
            $key = intdiv($c['time'], $bucketSeconds) * $bucketSeconds;
            if (!isset($buckets[$key])) {
                $buckets[$key] = ['time'=>$key,'open'=>$c['open'],'high'=>$c['high'],'low'=>$c['low'],'close'=>$c['close'],'volume'=>(float)($c['volume']??0)];
            } else {
                $buckets[$key]['high'] = max($buckets[$key]['high'], $c['high']);
                $buckets[$key]['low'] = min($buckets[$key]['low'], $c['low']);
                $buckets[$key]['close'] = $c['close'];
                $buckets[$key]['volume'] += (float)($c['volume']??0);
            }
        }
        return array_values($buckets);
    }

    private function fromTwelveData(Market $market, string $timeframe, int $count): array
    {
        $interval = match ($timeframe) {'M1'=>'1min','M5'=>'5min','M15'=>'15min','H4'=>'4h','D1'=>'1day',default=>'1h'};
        $symbol = match ($market->category) {
            'crypto' => substr($market->symbol,0,-3).'/USD',
            'indices' => $market->symbol,
            default => substr($market->symbol,0,3).'/'.substr($market->symbol,3),
        };
        $response = Http::timeout(12)->get('https://api.twelvedata.com/time_series', [
            'symbol'=>$symbol,'interval'=>$interval,'outputsize'=>$count,'apikey'=>config('forex.twelvedata_key'),
        ]);
        if (!$response->successful()) throw new \RuntimeException('HTTP '.$response->status());
        $values = $response->json('values');
        if (!is_array($values)) throw new \RuntimeException($response->json('message','Invalid response'));
        return collect($values)->map(fn($v)=>['time'=>strtotime($v['datetime']),'open'=>(float)$v['open'],'high'=>(float)$v['high'],'low'=>(float)$v['low'],'close'=>(float)$v['close'],'volume'=>(float)($v['volume']??0)])->sortBy('time')->values()->all();
    }

    private function recordFeed(Market $market, string $source, string $status, array $candles, ?string $error = null): void
    {
        $last = end($candles);
        $market->forceFill([
            'price' => $last['close'], 'data_source' => $source, 'data_status' => $status,
            'price_fetched_at' => now(), 'feed_error' => $error,
        ])->save();
    }

    private function demoCandles(Market $market, string $timeframe, int $count): array
    {
        $step = match($timeframe){'M1'=>60,'M5'=>300,'M15'=>900,'H4'=>14400,'D1'=>86400,default=>3600};
        mt_srand(crc32($market->symbol.now()->format('Y-m-d')));
        $price = $market->price > 0 ? $market->price : 100.0; $vol = $price * .0018; $end = now()->startOfHour()->timestamp;
        $closes = [$price];
        for($i=1;$i<$count;$i++) $closes[] = max(.0001,$closes[$i-1]-(mt_rand(-1000,1000)/1000)*$vol*.35);
        $closes=array_reverse($closes); $out=[];
        for($i=0;$i<$count;$i++){ $close=$closes[$i];$open=$i?$closes[$i-1]:$close;$wick=abs(mt_rand(200,1000)/1000)*$vol;$out[]=['time'=>$end-($count-1-$i)*$step,'open'=>round($open,5),'high'=>round(max($open,$close)+$wick,5),'low'=>round(min($open,$close)-$wick,5),'close'=>round($close,5),'volume'=>mt_rand(100,5000)]; }
        return $out;
    }
}
