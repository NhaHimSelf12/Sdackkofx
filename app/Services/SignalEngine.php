<?php

namespace App\Services;

use App\Domain\Strategies\StrategyRegistry;
use App\Models\Market;
use App\Models\Signal;

/**
 * Generates signals only from a verified remote feed (live or delayed).
 * Demo candles are explicitly rejected and existing signals are expired.
 */
class SignalEngine
{
    public function __construct(
        private MarketDataService $marketData,
        private AiMarketAnalysisService $analysis,
        private TrendlineDetector $trendlines,
    ) {}

    public function scan(Market $market, ?string $timeframe = null, bool $fresh = false): array
    {
        $timeframe ??= config('forex.default_timeframe', 'H1');
        $candles = $this->marketData->candles($market, $timeframe, null, $fresh);
        $market->refresh();

        // Never create trading signals from synthetic/demo candles.
        if ($market->data_status === 'demo') {
            $market->signals()->where('status', 'active')->update(['status' => 'expired']);
            $market->update([
                'ai_bias' => 'neutral', 'ai_confidence' => 0,
                'ai_summary' => 'Signal generation disabled because the market feed is DEMO. Restore a live or delayed provider first.',
                'analyzed_at' => now(),
            ]);
            return [];
        }

        $ai = $this->analysis->analyze($market, $candles);
        $last = end($candles); $first = $candles[max(0, count($candles) - 25)];
        $market->update([
            'price' => $last['close'],
            'change_pct' => $first['close'] != 0.0 ? round((($last['close']-$first['close'])/$first['close'])*100,3) : 0,
            'ai_bias'=>$ai['bias'],'ai_confidence'=>$ai['confidence'],'ai_summary'=>$ai['summary'],
            'analysis_details'=>$ai['details'] ?? null,
            'key_levels'=>$ai['key_levels'],'analyzed_at'=>now(),
        ]);

        $market->signals()->where('status','active')->update(['status'=>'expired']);
        $expiresAt = now()->add(match($timeframe){'M15'=>new \DateInterval('PT1H'),'H4'=>new \DateInterval('PT12H'),'D1'=>new \DateInterval('P3D'),default=>new \DateInterval('PT4H')});
        $created = [];

        foreach (StrategyRegistry::all() as $strategy) {
            $result = $strategy->analyze($candles);
            if ($result === null) continue;
            $risk = abs($result['entry']-$result['stop_loss']);
            $reward = abs($result['take_profit']-$result['entry']);
            // Reject malformed or directionally invalid plans.
            $valid = $risk > 0 && ($result['direction']==='buy'
                ? $result['stop_loss'] < $result['entry'] && $result['take_profit'] > $result['entry']
                : $result['stop_loss'] > $result['entry'] && $result['take_profit'] < $result['entry']);
            if (!$valid) continue;

            // Strict trend filter: reject counter-trend signals
            // (e.g., selling when AI bias is bullish, especially important for XAUUSD)
            if ($ai['bias'] === 'bullish' && $result['direction'] === 'sell') continue;
            if ($ai['bias'] === 'bearish' && $result['direction'] === 'buy') continue;

            $created[] = Signal::create([
                'market_id'=>$market->id,'strategy'=>$strategy->code(),'timeframe'=>$timeframe,
                'direction'=>$result['direction'],'entry'=>round($result['entry'],5),
                'stop_loss'=>round($result['stop_loss'],5),'take_profit'=>round($result['take_profit'],5),
                'risk_reward'=>round($reward/$risk,2),'confidence'=>$result['confidence'],'status'=>'active',
                'data_source'=>$market->data_source,'data_status'=>$market->data_status,
                'feed_price'=>$last['close'],'generated_at'=>now(),'expires_at'=>$expiresAt,'note'=>$result['note'],
            ]);
        }

        // ONE clear decision per market: mark the strongest signal aligned with
        // the strategy majority as the primary trade plan.
        if ($created !== []) {
            $buys = count(array_filter($created, fn($s) => $s->direction === 'buy'));
            $sells = count($created) - $buys;
            $majority = $buys >= $sells ? 'buy' : 'sell';
            $aligned = array_values(array_filter($created, fn($s) => $s->direction === $majority));
            $pool = $aligned !== [] ? $aligned : $created;
            usort($pool, fn($a, $b) => $b->confidence <=> $a->confidence);
            $primary = $pool[0];
            $agree = count($aligned);
            
            // Check for recent high-impact news related to this market
            $news = \App\Models\NewsItem::where('published_at', '>=', now()->subHours(12))
                ->where('impact', 'high')
                ->where('symbols', 'LIKE', '%"'.$market->symbol.'"%')
                ->latest('published_at')
                ->first();
                
            $newsWarning = '';
            $confidenceAdjust = 0;
            if ($news) {
                $newsWarning = sprintf(' ⚠️ NEWS: High-impact news detected ("%s"). Expect volatility.', $news->title);
                // Reduce confidence slightly if news sentiment contradicts the signal, or just general volatility risk
                $confidenceAdjust = ($news->sentiment === 'neutral' || $news->sentiment === $majority) ? -5 : -15;
            }

            $primary->update([
                'is_primary' => true,
                'confidence' => min(96, max(20, $primary->confidence + max(0, ($agree - 1) * 4) + $confidenceAdjust)),
                'note' => $primary->note.sprintf(' Confluence: %d of %d strategies agree on %s.', max(1, $agree), count($created), strtoupper($majority)) . $newsWarning,
            ]);
        }

        $this->trendlines->detect($market, $candles, $timeframe);
        return $created;
    }
}
