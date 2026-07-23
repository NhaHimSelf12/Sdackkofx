<?php

namespace App\Domain\Strategies;

use App\Domain\Strategies\Concerns\UsesSwings;

/**
 * Smart Money Concepts: break of structure (BOS) / change of character
 * (CHoCH), order blocks and liquidity sweeps. Simplified rule set:
 * - Bullish BOS (close above last swing high) after a sweep of the previous
 *   swing low => buy from the origin order block.
 * - Bearish BOS (close below last swing low) after a sweep of the previous
 *   swing high => sell from the origin order block.
 */
class SmcStrategy implements StrategyInterface
{
    use UsesSwings;

    public function code(): string
    {
        return 'SMC';
    }

    public function name(): string
    {
        return 'Smart Money Concepts';
    }

    public function description(): string
    {
        return 'Trades institutional order flow: break of structure (BOS) and change of character (CHoCH) confirmed by liquidity sweeps, with entries from the origin order block.';
    }

    public function concepts(): array
    {
        return ['BOS / CHoCH', 'Order blocks', 'Liquidity sweeps', 'Premium & discount', 'Inducement'];
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
        $lastLow = end($lows)['price'];
        $prevLow = prev($lows)['price'];
        $prevHigh = prev($highs)['price'];

        // Bullish BOS after liquidity sweep of the previous low
        if ($close > $lastHigh && $lastLow < $prevLow) {
            $stop = $lastLow - 0.25 * $atr; // below the sweep
            // Wait for a pullback to the premium/discount zone of the breakout
            $entry = $lastHigh - 0.4 * $atr; 
            if ($close < $entry) $entry = $close;
            $target = $entry + 2.2 * ($entry - $stop);

            return [
                'direction' => 'buy',
                'entry' => $entry,
                'stop_loss' => $stop,
                'take_profit' => $target,
                'confidence' => 78,
                'note' => 'Bullish BOS above swing high after sell-side liquidity sweep; placing Buy Limit for order-block retest.',
            ];
        }

        // Bearish BOS after liquidity sweep of the previous high
        if ($close < $lastLow && $lastHigh > $prevHigh) {
            $stop = $lastHigh + 0.25 * $atr;
            // Wait for a pullback to the premium/discount zone of the breakout
            $entry = $lastLow + 0.4 * $atr;
            if ($close > $entry) $entry = $close;
            $target = $entry - 2.2 * ($stop - $entry);

            return [
                'direction' => 'sell',
                'entry' => $entry,
                'stop_loss' => $stop,
                'take_profit' => $target,
                'confidence' => 78,
                'note' => 'Bearish CHoCH below swing low after buy-side liquidity sweep; placing Sell Limit for order-block retest.',
            ];
        }

        return null;
    }
}
