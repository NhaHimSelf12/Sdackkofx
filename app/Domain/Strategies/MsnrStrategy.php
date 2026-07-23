<?php

namespace App\Domain\Strategies;

use App\Domain\Strategies\Concerns\UsesSwings;

/**
 * MSNR: Market Structure + Support & Resistance. Simplified rule set:
 * - Uptrend structure (higher highs & higher lows) with price rejecting a
 *   support zone => buy the bounce toward the last high.
 * - Downtrend structure with price rejecting a resistance zone => sell the
 *   rejection toward the last low.
 */
class MsnrStrategy implements StrategyInterface
{
    use UsesSwings;

    public function code(): string
    {
        return 'MSNR';
    }

    public function name(): string
    {
        return 'Market Structure & S/R';
    }

    public function description(): string
    {
        return 'Follows market structure (higher highs / lower lows) and trades bounces and rejections at horizontal support and resistance zones.';
    }

    public function concepts(): array
    {
        return ['Market structure', 'Support & resistance', 'Trend continuation', 'Zone rejections', 'Role reversal (flip zones)'];
    }

    public function analyze(array $candles): ?array
    {
        [$highs, $lows] = $this->swings($candles);
        if (count($highs) < 2 || count($lows) < 2) {
            return null;
        }

        $close = $this->lastClose($candles);
        $atr = $this->atr($candles);
        $lastHigh = end($highs)['price'];
        $prevHigh = prev($highs)['price'];
        $lastLow = end($lows)['price'];
        $prevLow = prev($lows)['price'];

        $uptrend = $lastHigh > $prevHigh && $lastLow > $prevLow;
        $downtrend = $lastHigh < $prevHigh && $lastLow < $prevLow;

        // Buy the support bounce in an uptrend
        if ($uptrend && abs($close - $lastLow) <= 1.5 * $atr) {
            // Wait for price to touch near support (Limit Order)
            $entry = $lastLow + 0.3 * $atr;
            if ($close < $entry) $entry = $close; // If already lower, take current
            $stop = $lastLow - 0.8 * $atr;

            return [
                'direction' => 'buy',
                'entry' => $entry,
                'stop_loss' => $stop,
                'take_profit' => $lastHigh,
                'confidence' => 68,
                'note' => 'Uptrend structure (HH/HL) with price approaching support zone; placing Buy Limit near support.',
            ];
        }

        // Sell the resistance rejection in a downtrend
        if ($downtrend && abs($lastHigh - $close) <= 1.5 * $atr) {
            // Wait for price to touch near resistance (Limit Order)
            $entry = $lastHigh - 0.3 * $atr;
            if ($close > $entry) $entry = $close; // If already higher, take current
            $stop = $lastHigh + 0.8 * $atr;

            return [
                'direction' => 'sell',
                'entry' => $entry,
                'stop_loss' => $stop,
                'take_profit' => $lastLow,
                'confidence' => 68,
                'note' => 'Downtrend structure (LL/LH) with price approaching resistance zone; placing Sell Limit near resistance.',
            ];
        }

        return null;
    }
}
