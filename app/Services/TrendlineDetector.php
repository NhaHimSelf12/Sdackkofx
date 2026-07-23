<?php

namespace App\Services;

use App\Models\Market;
use App\Models\Trendline;

/**
 * Detects buy-side (support / ascending) and sell-side (resistance /
 * descending) trendlines from swing points and stores them per market.
 */
class TrendlineDetector
{
    public function __construct(private AiMarketAnalysisService $analysis)
    {
    }

    /**
     * @param array<int, array{time:int, open:float, high:float, low:float, close:float}> $candles
     */
    public function detect(Market $market, array $candles, string $timeframe = 'H1'): int
    {
        [$highs, $lows] = $this->analysis->swings($candles);

        $market->trendlines()->where('timeframe', $timeframe)->delete();
        $created = 0;

        // Sell-side trendline: connect the last two swing highs.
        if (count($highs) >= 2) {
            $a = $highs[count($highs) - 2];
            $b = $highs[count($highs) - 1];
            Trendline::create([
                'market_id' => $market->id,
                'kind' => 'trend',
                'direction' => 'down',
                'timeframe' => $timeframe,
                'start_time' => $a['time'], 'start_price' => $a['price'],
                'end_time' => $b['time'], 'end_price' => $b['price'],
                'touches' => 2,
            ]);
            $created++;
        }

        // Buy-side trendline: connect the last two swing lows.
        if (count($lows) >= 2) {
            $a = $lows[count($lows) - 2];
            $b = $lows[count($lows) - 1];
            Trendline::create([
                'market_id' => $market->id,
                'kind' => 'trend',
                'direction' => 'up',
                'timeframe' => $timeframe,
                'start_time' => $a['time'], 'start_price' => $a['price'],
                'end_time' => $b['time'], 'end_price' => $b['price'],
                'touches' => 2,
            ]);
            $created++;
        }

        // Horizontal support & resistance from the most recent swings.
        $lastTime = end($candles)['time'];
        foreach (array_slice($highs, -2) as $high) {
            Trendline::create([
                'market_id' => $market->id,
                'kind' => 'resistance',
                'direction' => 'flat',
                'timeframe' => $timeframe,
                'start_time' => $high['time'], 'start_price' => $high['price'],
                'end_time' => $lastTime, 'end_price' => $high['price'],
                'touches' => 1,
            ]);
            $created++;
        }
        foreach (array_slice($lows, -2) as $low) {
            Trendline::create([
                'market_id' => $market->id,
                'kind' => 'support',
                'direction' => 'flat',
                'timeframe' => $timeframe,
                'start_time' => $low['time'], 'start_price' => $low['price'],
                'end_time' => $lastTime, 'end_price' => $low['price'],
                'touches' => 1,
            ]);
            $created++;
        }

        return $created;
    }
}
