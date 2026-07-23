<?php

namespace App\Domain\Strategies;

use App\Domain\Strategies\Concerns\UsesSwings;

/**
 * ICT (Inner Circle Trader): fair value gaps (FVG) and premium/discount of
 * the current dealing range. Simplified rule set:
 * - Price trading in discount (below range 50%) with an unfilled bullish FVG
 *   => buy from the FVG midpoint targeting range highs.
 * - Price trading in premium with an unfilled bearish FVG => sell from the
 *   FVG midpoint targeting range lows.
 */
class IctStrategy implements StrategyInterface
{
    use UsesSwings;

    public function code(): string
    {
        return 'ICT';
    }

    public function name(): string
    {
        return 'ICT Concepts';
    }

    public function description(): string
    {
        return 'Trades fair value gaps inside premium/discount of the dealing range, in line with draw on liquidity — the Inner Circle Trader model.';
    }

    public function concepts(): array
    {
        return ['Fair value gaps', 'Premium / discount', 'Dealing range', 'Draw on liquidity', 'Kill zones'];
    }

    public function analyze(array $candles): ?array
    {
        $recent = array_slice($candles, -80);
        $rangeHigh = max(array_column($recent, 'high'));
        $rangeLow = min(array_column($recent, 'low'));
        $mid = ($rangeHigh + $rangeLow) / 2;
        $close = $this->lastClose($candles);
        $atr = $this->atr($candles);

        // Find the most recent unfilled FVG (3-candle imbalance)
        for ($i = count($candles) - 3; $i >= count($candles) - 40 && $i >= 2; $i--) {
            $a = $candles[$i - 2];
            $c = $candles[$i];

            // Bullish FVG: candle A high < candle C low
            if ($a['high'] < $c['low'] && $close < $mid) {
                $fvgMid = ($a['high'] + $c['low']) / 2;
                if ($close > $fvgMid) {
                    $stop = $fvgMid - 1.2 * $atr;

                    return [
                        'direction' => 'buy',
                        'entry' => $fvgMid,
                        'stop_loss' => $stop,
                        'take_profit' => $rangeHigh,
                        'confidence' => 72,
                        'note' => 'Price in discount with an unfilled bullish FVG; buying the gap midpoint, draw on liquidity at range highs.',
                    ];
                }
            }

            // Bearish FVG: candle A low > candle C high
            if ($a['low'] > $c['high'] && $close > $mid) {
                $fvgMid = ($a['low'] + $c['high']) / 2;
                if ($close < $fvgMid) {
                    $stop = $fvgMid + 1.2 * $atr;

                    return [
                        'direction' => 'sell',
                        'entry' => $fvgMid,
                        'stop_loss' => $stop,
                        'take_profit' => $rangeLow,
                        'confidence' => 72,
                        'note' => 'Price in premium with an unfilled bearish FVG; selling the gap midpoint, draw on liquidity at range lows.',
                    ];
                }
            }
        }

        return null;
    }
}
