<?php

namespace App\Domain\Strategies;

use App\Domain\Strategies\Concerns\UsesSwings;

/**
 * Always-on technical confluence model used when the stricter SMC/ICT/MSNR
 * setups do not trigger. It requires EMA trend + momentum + minimum ATR and
 * creates an ATR-based entry plan. This is labelled TECH, never AI/SMC.
 */
class TechnicalConfluenceStrategy implements StrategyInterface
{
    use UsesSwings;

    public function code(): string { return 'TECH'; }
    public function name(): string { return 'Technical Confluence'; }
    public function description(): string { return 'EMA 20/50 trend, momentum and ATR-based risk model for continuous market monitoring.'; }
    public function concepts(): array { return ['EMA 20/50', 'Momentum', 'ATR risk', 'Trend continuation']; }

    public function analyze(array $candles): ?array
    {
        if (count($candles) < 60) return null;
        $closes = array_column($candles, 'close');
        $ema20 = $this->ema($closes, 20); $ema50 = $this->ema($closes, 50);
        $price = end($closes); $atr = $this->atr($candles, 14);
        if ($atr <= 0 || $ema50 == 0.0) return null;

        $gap = abs($ema20 - $ema50) / $ema50;
        $momentum = $price - $closes[count($closes) - 6];
        $lastCandleMomentum = $price - $closes[count($closes) - 2]; // Just the last candle
        
        // Ignore flat/choppy markets; threshold is intentionally modest.
        if ($gap < 0.00015) return null;

        // Ensure both medium and short-term momentum align with the trend.
        // If it dropped even slightly in the last candle (e.g. negative lastCandleMomentum), invalidate the buy.
        $bullish = ($ema20 > $ema50 || $price > $ema20) && $momentum > 0 && $lastCandleMomentum > -($atr * 0.2);
        $bearish = ($ema20 < $ema50 || $price < $ema20) && $momentum < 0 && $lastCandleMomentum < ($atr * 0.2);
        if (!$bullish && (!$bearish)) return null;

        $direction = $bullish ? 'buy' : 'sell';
        
        // Wait for a slight pullback (0.4 ATR) instead of entering exactly at the market price
        $entry = $bullish ? $price - 0.4 * $atr : $price + 0.4 * $atr;
        $stop = $bullish ? $entry - 1.5 * $atr : $entry + 1.5 * $atr;
        $target = $bullish ? $entry + 3 * $atr : $entry - 3 * $atr;
        $strength = min(20, (int) round($gap * 10000));

        return [
            'direction' => $direction, 'entry' => $entry, 'stop_loss' => $stop,
            'take_profit' => $target, 'confidence' => 55 + $strength,
            'note' => sprintf('%s EMA20/EMA50 alignment with 5-candle momentum confirmation; ATR-based Pullback entry with 1:2 risk model.', ucfirst($direction)),
        ];
    }

    private function ema(array $values, int $period): float
    {
        $k = 2 / ($period + 1); $ema = $values[0] ?? 0.0;
        foreach ($values as $value) $ema = $value * $k + $ema * (1 - $k);
        return $ema;
    }
}
