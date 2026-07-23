<?php

namespace App\Services;

use App\Models\Market;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * AI market analysis.
 *
 * With OPENAI_API_KEY set, an LLM writes the analysis summary from computed
 * technical facts. Without it, the built-in technical engine produces the
 * bias, confidence, key levels and a readable summary on its own.
 */
class AiMarketAnalysisService
{
    /**
     * @param array<int, array{time:int, open:float, high:float, low:float, close:float}> $candles
     * @return array{bias:string, confidence:int, summary:string, key_levels:array}
     */
    public function analyze(Market $market, array $candles): array
    {
        $facts = $this->technicalFacts($candles);

        $emaBullish = $facts['ema_fast'] > $facts['ema_slow'];

        if ($emaBullish) {
            $bias = ($facts['rsi'] > 68 || $facts['rsi'] < 45 || $facts['price'] < $facts['ema_fast'] || $facts['structure'] === 'bearish') ? 'neutral' : 'bullish';
        } else {
            $bias = ($facts['rsi'] < 32 || $facts['rsi'] > 55 || $facts['price'] > $facts['ema_fast'] || $facts['structure'] === 'bullish') ? 'neutral' : 'bearish';
        }

        $confidence = (int) min(95, max(35,
            50
            + abs($facts['ema_gap_pct']) * 900
            + ($facts['structure'] === $bias ? 15 : -10)
        ));

        $summary = $this->llmSummary($market, $facts, $bias)
            ?? $this->heuristicSummary($market, $facts, $bias, $confidence);

        return [
            'bias' => $bias,
            'confidence' => $confidence,
            'summary' => $summary,
            'details' => $this->details($facts, $bias, $confidence),
            'key_levels' => [
                'support' => $facts['supports'],
                'resistance' => $facts['resistances'],
            ],
        ];
    }

    /**
     * Structured checklist used by the terminal so traders can see WHY
     * the engine holds the current bias.
     */
    private function details(array $facts, string $bias, int $confidence): array
    {
        $trendUp = $facts['ema_fast'] > $facts['ema_slow'];
        $rsi = $facts['rsi'];
        $rsiState = $rsi > 68 ? 'overbought' : ($rsi < 32 ? 'oversold' : 'healthy');

        $verdict = match (true) {
            $bias === 'bullish' && $confidence >= 70 => 'Clear BUY environment — follow the primary plan below.',
            $bias === 'bearish' && $confidence >= 70 => 'Clear SELL environment — follow the primary plan below.',
            $bias === 'neutral' => 'No clear edge right now — staying flat is a valid position.',
            default => ucfirst($bias).' bias but conviction is moderate — wait for the primary plan trigger.',
        };

        return [
            'checks' => [
                ['label' => 'Trend', 'value' => $trendUp ? 'Up · EMA20 above EMA50' : 'Down · EMA20 below EMA50', 'state' => $trendUp ? 'buy' : 'sell'],
                ['label' => 'Momentum', 'value' => sprintf('RSI %.0f · %s', $rsi, $rsiState), 'state' => $rsiState === 'healthy' ? ($trendUp ? 'buy' : 'sell') : 'caution'],
                ['label' => 'Structure', 'value' => ucfirst($facts['structure']).' swing structure', 'state' => $facts['structure'] === 'bullish' ? 'buy' : ($facts['structure'] === 'bearish' ? 'sell' : 'caution')],
                ['label' => 'Key levels', 'value' => sprintf('S %s · R %s', $facts['supports'][0] ?? 'n/a', $facts['resistances'][0] ?? 'n/a'), 'state' => 'info'],
            ],
            'verdict' => $verdict,
        ];
    }

    /** Compute indicator facts used by both the LLM and the fallback engine. */
    private function technicalFacts(array $candles): array
    {
        $closes = array_column($candles, 'close');
        $lows = array_column($candles, 'low');
        $highs = array_column($candles, 'high');

        $emaFast = $this->ema($closes, 20);
        $emaSlow = $this->ema($closes, 50);
        $last = end($closes);

        // Swing-based structure: compare last two swing highs/lows
        [$swingHighs, $swingLows] = $this->swings($candles);
        $structure = 'neutral';
        if (count($swingHighs) >= 2 && count($swingLows) >= 2) {
            $hh = end($swingHighs)['price'] > prev($swingHighs)['price'];
            $hl = end($swingLows)['price'] > prev($swingLows)['price'];
            $structure = $hh && $hl ? 'bullish' : (! $hh && ! $hl ? 'bearish' : 'neutral');
        }

        return [
            'price' => $last,
            'ema_fast' => $emaFast,
            'ema_slow' => $emaSlow,
            'ema_gap_pct' => $emaSlow != 0.0 ? ($emaFast - $emaSlow) / $emaSlow : 0,
            'rsi' => $this->rsi($closes, 14),
            'structure' => $structure,
            'supports' => collect($swingLows)->sortByDesc('time')->take(3)->pluck('price')->map(fn ($p) => round($p, 5))->values()->all(),
            'resistances' => collect($swingHighs)->sortByDesc('time')->take(3)->pluck('price')->map(fn ($p) => round($p, 5))->values()->all(),
            'range_high' => max($highs),
            'range_low' => min($lows),
        ];
    }

    private function llmSummary(Market $market, array $facts, string $bias): ?string
    {
        $key = config('forex.openai_key');
        if (! $key) {
            return null;
        }

        try {
            $response = Http::withToken($key)
                ->timeout(20)
                ->post('https://api.openai.com/v1/chat/completions', [
                    'model' => config('forex.openai_model'),
                    'messages' => [
                        ['role' => 'system', 'content' => 'You are a professional forex analyst. Write a concise 2-3 sentence market analysis. No financial advice disclaimers.'],
                        ['role' => 'user', 'content' => sprintf(
                            '%s (%s). Price %.5f, EMA20 %.5f, EMA50 %.5f, RSI %.1f, structure %s, bias %s, nearest support %s, nearest resistance %s.',
                            $market->symbol, $market->name, $facts['price'], $facts['ema_fast'], $facts['ema_slow'],
                            $facts['rsi'], $facts['structure'], $bias,
                            $facts['supports'][0] ?? 'n/a', $facts['resistances'][0] ?? 'n/a',
                        )],
                    ],
                    'max_tokens' => 160,
                ]);

            return $response->json('choices.0.message.content');
        } catch (\Throwable $e) {
            Log::warning('OpenAI analysis failed, using fallback', ['error' => $e->getMessage()]);

            return null;
        }
    }

    private function heuristicSummary(Market $market, array $facts, string $bias, int $confidence): string
    {
        $trend = $facts['ema_fast'] > $facts['ema_slow'] ? 'above' : 'below';
        $rsiState = $facts['rsi'] > 68 ? 'overbought' : ($facts['rsi'] < 32 ? 'oversold' : 'balanced');

        return sprintf(
            '%s is %s: EMA20 is %s EMA50 with %s market structure and RSI %.0f (%s). Watch support near %s and resistance near %s for the next %s opportunity.',
            $market->symbol,
            $bias,
            $trend,
            $facts['structure'],
            $facts['rsi'],
            $rsiState,
            $facts['supports'][0] ?? 'the range low',
            $facts['resistances'][0] ?? 'the range high',
            $bias === 'bearish' ? 'sell' : 'buy',
        );
    }

    private function ema(array $values, int $period): float
    {
        $k = 2 / ($period + 1);
        $ema = $values[0] ?? 0.0;
        foreach ($values as $v) {
            $ema = $v * $k + $ema * (1 - $k);
        }

        return $ema;
    }

    private function rsi(array $closes, int $period = 14): float
    {
        $gains = $losses = [];
        for ($i = 1; $i < count($closes); $i++) {
            $diff = $closes[$i] - $closes[$i - 1];
            $gains[] = max($diff, 0);
            $losses[] = max(-$diff, 0);
        }
        $gains = array_slice($gains, -$period);
        $losses = array_slice($losses, -$period);
        $avgGain = array_sum($gains) / max(1, count($gains));
        $avgLoss = array_sum($losses) / max(1, count($losses));

        if ($avgLoss == 0.0) {
            return 100.0;
        }

        return 100 - (100 / (1 + $avgGain / $avgLoss));
    }

    /**
     * Detect swing highs/lows with a simple fractal window.
     *
     * @return array{0: array<int, array{time:int, price:float}>, 1: array<int, array{time:int, price:float}>}
     */
    public function swings(array $candles, int $window = 5): array
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
}
