<?php

namespace App\Domain\Strategies\Concerns;

trait UsesSwings
{
    /**
     * @return array{0: array<int, array{time:int, price:float}>, 1: array<int, array{time:int, price:float}>}
     */
    protected function swings(array $candles, int $window = 5): array
    {
        $highs = $lows = [];
        $n = count($candles);
        for ($i = $window; $i < $n - $window; $i++) {
            $isHigh = $isLow = true;
            for ($j = $i - $window; $j <= $i + $window; $j++) {
                if ($candles[$j]['high'] > $candles[$i]['high']) {
                    $isHigh = false;
                }
                if ($candles[$j]['low'] < $candles[$i]['low']) {
                    $isLow = false;
                }
            }
            if ($isHigh) {
                $highs[] = ['time' => $candles[$i]['time'], 'price' => $candles[$i]['high']];
            }
            if ($isLow) {
                $lows[] = ['time' => $candles[$i]['time'], 'price' => $candles[$i]['low']];
            }
        }

        return [$highs, $lows];
    }

    /** Average true range of the last $period candles. */
    protected function atr(array $candles, int $period = 14): float
    {
        $slice = array_slice($candles, -$period);
        $sum = 0.0;
        foreach ($slice as $c) {
            $sum += $c['high'] - $c['low'];
        }

        return $sum / max(1, count($slice));
    }

    protected function lastClose(array $candles): float
    {
        return end($candles)['close'];
    }
}
