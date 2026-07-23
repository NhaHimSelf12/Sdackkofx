<?php

namespace App\Services;

use App\Models\EaBot;
use App\Models\EaBotTrade;
use App\Models\Market;
use App\Models\Signal;

/**
 * Paper-trading expert advisor engine.
 *
 * Bots execute SIMULATED positions and manage them against the live feed
 * price. No real broker orders are ever placed.
 *
 * Execution styles:
 * - scalping  -> reads live M1 candles itself and enters AT MARKET on
 *                1-candle momentum breakouts with tight ATR stops (fast).
 * - highlow   -> reads live M15 candles and trades breaks/rejections of the
 *                session high/low at market price.
 * - daytrade  -> waits for a confirmed PRIMARY signal from the signal engine.
 * - swing     -> highest conviction PRIMARY signals only.
 *
 * Position sizing follows the account: risk_amount = equity * risk_pct,
 * units = risk_amount / stop distance — so a $10 account trades small and a
 * $5000 account trades proportionally bigger.
 */
class EaBotEngine
{
    public const MIN_CAPITAL = 10;
    public const MAX_CAPITAL = 5000;

    public const MODES = [
        'scalping' => [
            'label' => 'Scalping', 'timeframe' => 'M1', 'max_per_day' => 20, 'per_run' => 2,
            'min_confidence' => 0, 'cooldown_minutes' => 1, 'primary_only' => false, 'style' => 'fast',
            'description' => 'SMC style on M1: buys pullbacks into discount lows, sells rallies into premium highs. Never chases breakouts. Many quick positions per day.',
        ],
        'highlow' => [
            'label' => 'High & Low', 'timeframe' => 'M15', 'max_per_day' => 6, 'per_run' => 1,
            'min_confidence' => 0, 'cooldown_minutes' => 45, 'primary_only' => false, 'style' => 'fast',
            'description' => 'Trades breaks and rejections of the session high/low on M15 at market price, 5-6 entries per day.',
        ],
        'daytrade' => [
            'label' => 'Day Trade', 'timeframe' => 'H1', 'max_per_day' => 3, 'per_run' => 1,
            'min_confidence' => 72, 'cooldown_minutes' => 120, 'primary_only' => true, 'style' => 'signal',
            'description' => 'Waits for a confirmed PRIMARY setup before entering. 2-3 entries per day.',
        ],
        'swing' => [
            'label' => 'Swing Trade', 'timeframe' => 'H4', 'max_per_day' => 2, 'per_run' => 1,
            'min_confidence' => 78, 'cooldown_minutes' => 240, 'primary_only' => true, 'style' => 'signal',
            'description' => 'Highest conviction PRIMARY signals only. 1-2 patient positions per day.',
        ],
    ];

    /** Human-readable reason why the last run did not open a trade. */
    private ?string $note = null;

    public function __construct(private MarketDataService $marketData)
    {
    }

    /** Run every bot that is currently switched on. */
    public function runAll(): array
    {
        $report = [];
        foreach (EaBot::where('status', 'running')->get() as $bot) {
            $report[$bot->name] = $this->run($bot);
        }

        return $report;
    }

    public function run(EaBot $bot): array
    {
        $config = self::MODES[$bot->mode] ?? self::MODES['daytrade'];
        $this->note = null;
        $closed = $this->manageOpenTrades($bot, $config);
        $opened = 0;
        if ($bot->status === 'running' && $this->entrySlotAvailable($bot, $config)) {
            $opened = $config['style'] === 'fast'
                ? $this->openFastTrades($bot, $config)
                : $this->openSignalTrades($bot, $config);
        }

        $bot->last_note = $opened > 0
            ? 'Opened '.$opened.' position(s) on the last run.'
            : ($this->note ?? 'Watching the market — no valid setup on the last run.');
        $bot->last_run_at = now();
        $bot->save();

        return ['opened' => $opened, 'closed' => $closed, 'note' => $bot->last_note];
    }

    /** Close open paper trades that hit their stop loss or take profit. */
    private function manageOpenTrades(EaBot $bot, array $config): int
    {
        $closed = 0;
        foreach ($bot->openTrades()->with('market')->get() as $trade) {
            $market = $trade->market;

            // Scalps live on tight stops: refresh the M1 price when stale.
            if ($config['style'] === 'fast' && ($market->price_fetched_at === null || $market->price_fetched_at->lt(now()->subSeconds(90)))) {
                $candles = $this->marketData->candles($market, 'M1', 30);
                $market->refresh();
                if (($market->data_status ?? 'demo') !== 'demo' && $candles !== []) {
                    $market->update(['price' => end($candles)['close']]);
                }
            }

            $price = (float) ($market->price ?? 0);
            if ($price <= 0 || ($market->data_status ?? 'demo') === 'demo') {
                continue; // never settle paper trades against demo prices
            }

            $buy = $trade->direction === 'buy';
            $hitSl = $buy ? $price <= $trade->stop_loss : $price >= $trade->stop_loss;
            $hitTp = $buy ? $price >= $trade->take_profit : $price <= $trade->take_profit;

            if ($hitSl || $hitTp) {
                $exit = $hitSl ? $trade->stop_loss : $trade->take_profit;
                $pnl = round(($buy ? $exit - $trade->entry : $trade->entry - $exit) * $trade->units, 2);
                $trade->update(['status' => $hitSl ? 'lost' : 'won', 'pnl' => $pnl, 'closed_at' => now()]);
                $this->applyResult($bot, $hitSl ? 'lost' : 'won', $pnl);
                $closed++;
            } elseif ($trade->opened_at->lt(now()->subDays(3))) {
                // Timed exit: do not hold stale paper positions forever.
                $pnl = round(($buy ? $price - $trade->entry : $trade->entry - $price) * $trade->units, 2);
                $trade->update(['status' => 'closed', 'pnl' => $pnl, 'closed_at' => now(), 'note' => trim(($trade->note ?? '').' Timed exit after 3 days.')]);
                $this->applyResult($bot, $pnl >= 0 ? 'won' : 'lost', $pnl);
                $closed++;
            }
        }

        return $closed;
    }

    private function applyResult(EaBot $bot, string $result, float $pnl): void
    {
        $bot->trades++;
        $result === 'won' ? $bot->wins++ : $bot->losses++;
        $bot->pnl = round($bot->pnl + $pnl, 2);
        $bot->save();
    }

    /** Daily quota + cooldown gate shared by both execution styles. */
    private function entrySlotAvailable(EaBot $bot, array $config): bool
    {
        // Reset the daily counter on a new calendar day.
        if ($bot->last_trade_date === null || ! $bot->last_trade_date->isToday()) {
            $bot->positions_today = 0;
        }

        if ($bot->positions_today >= $config['max_per_day']) {
            $this->note = 'Daily quota reached ('.$bot->positions_today.'/'.$config['max_per_day'].') — resets on the next trading day.';

            return false;
        }

        $lastOpen = $bot->trades()->latest('opened_at')->value('opened_at');
        if ($lastOpen !== null && now()->diffInMinutes($lastOpen, true) < $config['cooldown_minutes']) {
            $this->note = 'Cooldown — waits '.$config['cooldown_minutes'].' min between entries.';

            return false;
        }

        return true;
    }

    /**
     * Fast style (scalping / high-low): read live candles directly and enter
     * AT MARKET on the current price — no waiting for the H1 signal scan.
     */
    private function openFastTrades(EaBot $bot, array $config): int
    {
        $markets = $bot->market_id
            ? Market::whereKey($bot->market_id)->get()
            : Market::orderBy('symbol')->get();

        $openMarkets = $bot->openTrades()->pluck('market_id')->all();
        $slots = min($config['per_run'], $config['max_per_day'] - $bot->positions_today);
        $opened = 0;
        $demo = 0;

        foreach ($markets as $market) {
            if ($opened >= $slots) {
                break;
            }
            if (in_array($market->id, $openMarkets, true)) {
                continue; // one open position per market per bot
            }

            $candles = $this->marketData->candles($market, $config['timeframe'], 96);
            $market->refresh();
            if (($market->data_status ?? 'demo') === 'demo' || count($candles) < 30) {
                $demo++;

                continue; // demo feed is never traded
            }

            $plan = $bot->mode === 'scalping'
                ? $this->scalpPlan($candles)
                : $this->highLowPlan($candles);
            if ($plan === null) {
                continue;
            }

            if ($this->openTrade($bot, $market, $plan, null)) {
                $opened++;
            }
        }

        if ($opened === 0) {
            $this->note = $demo >= count($markets)
                ? 'Feed is DEMO — the bot refuses to trade demo prices. Configure a market provider.'
                : ($bot->mode === 'scalping'
                    ? 'Watching M1 — waiting for an SMC pullback: dip into swing lows / EMA21 to BUY, rally into swing highs / EMA21 to SELL.'
                    : 'Watching M15 — waiting for a break or rejection of the session high/low.');
        }

        return $opened;
    }

    /**
     * Scalping brain — SMC style on M1. Never chases breakouts:
     * BUY  only from DISCOUNT: uptrend pullback that sweeps recent swing lows
     *      (grabs sell-side liquidity) and closes back up — buy from below.
     * SELL only from PREMIUM: downtrend rally that sweeps recent swing highs
     *      (grabs buy-side liquidity) and closes back down — sell from above.
     * Stop hides behind the swept swing; target is 1.5R.
     */
    private function scalpPlan(array $candles): ?array
    {
        $last = $candles[count($candles) - 1];
        $recent = array_slice($candles, -3); // reaction window: live candle + two before it
        $prior = array_slice($candles, -18, 15); // swing structure before the reaction window
        $swingHi = max(array_column($prior, 'high'));
        $swingLo = min(array_column($prior, 'low'));
        $range = array_slice($candles, -40);
        $eq = (max(array_column($range, 'high')) + min(array_column($range, 'low'))) / 2; // equilibrium
        $closes = array_column($candles, 'close');
        $ema9 = $this->ema($closes, 9);
        $ema21 = $this->ema($closes, 21);
        $atr = $this->atr($candles, 14);
        if ($atr <= 0) {
            return null;
        }

        $recentLo = min(array_column($recent, 'low'));
        $recentHi = max(array_column($recent, 'high'));
        $sweptLows = $recentLo <= $swingLo + $atr * 0.35; // grabbed sell-side liquidity
        $sweptHighs = $recentHi >= $swingHi - $atr * 0.35; // grabbed buy-side liquidity
        $dipToEma = $recentLo <= $ema21 + $atr * 0.25; // discount pullback into dynamic support
        $rallyToEma = $recentHi >= $ema21 - $atr * 0.25; // premium rally into dynamic resistance

        // BUY from below: uptrend + pullback into the lows (sweep) or EMA21 +
        // bullish reaction close + not chasing above equilibrium.
        $buy = $ema9 > $ema21
            && ($sweptLows || $dipToEma)
            && $last['close'] > $last['open']
            && $last['close'] <= $eq + $atr * 0.6;

        // SELL from above: downtrend + rally into the highs (sweep) or EMA21 +
        // bearish rejection close + not chasing below equilibrium.
        $sell = $ema9 < $ema21
            && ($sweptHighs || $rallyToEma)
            && $last['close'] < $last['open']
            && $last['close'] >= $eq - $atr * 0.6;

        if (! $buy && ! $sell) {
            return null;
        }

        $entry = $last['close'];
        if ($buy) {
            $sl = min($recentLo, $sweptLows ? $swingLo : $recentLo) - $atr * 0.5; // behind the sweep/pullback low
            $tp = $entry + ($entry - $sl) * 1.5;
        } else {
            $sl = max($recentHi, $sweptHighs ? $swingHi : $recentHi) + $atr * 0.5;
            $tp = $entry - ($sl - $entry) * 1.5;
        }

        return [
            'direction' => $buy ? 'buy' : 'sell',
            'entry' => $entry, 'stop_loss' => $sl, 'take_profit' => $tp,
            'note' => $buy
                ? 'SCALP M1 SMC entry: BUY from discount — '.($sweptLows ? 'sweep of sell-side liquidity below swing lows' : 'pullback into EMA21 demand').', bullish reaction close, stop behind the low, 1.5R target (paper trade)'
                : 'SCALP M1 SMC entry: SELL from premium — '.($sweptHighs ? 'sweep of buy-side liquidity above swing highs' : 'rally into EMA21 supply').', bearish rejection close, stop behind the high, 1.5R target (paper trade)',
        ];
    }

    /**
     * High & Low brain: break or rejection of the recent session high/low on
     * M15 (last ~8 hours), entry at the live close.
     */
    private function highLowPlan(array $candles): ?array
    {
        $last = $candles[count($candles) - 1];
        $window = array_slice($candles, -33, 32); // ~8h of M15, excluding the live candle
        array_pop($window);
        $hi = max(array_column($window, 'high'));
        $lo = min(array_column($window, 'low'));
        $atr = $this->atr($candles, 14);
        if ($atr <= 0) {
            return null;
        }

        $bullBody = $last['close'] > $last['open'];
        $bearBody = $last['close'] < $last['open'];

        $plan = null;
        if ($last['close'] > $hi && $bullBody) {
            $plan = ['buy', 'break above session high'];
        } elseif ($last['high'] >= $hi - $atr * 0.15 && $last['close'] < $hi - $atr * 0.3 && $bearBody) {
            $plan = ['sell', 'rejection of session high'];
        } elseif ($last['close'] < $lo && $bearBody) {
            $plan = ['sell', 'break below session low'];
        } elseif ($last['low'] <= $lo + $atr * 0.15 && $last['close'] > $lo + $atr * 0.3 && $bullBody) {
            $plan = ['buy', 'rejection of session low'];
        }
        if ($plan === null) {
            return null;
        }

        [$direction, $reason] = $plan;
        $entry = $last['close'];
        $buy = $direction === 'buy';
        $sl = $buy ? $entry - $atr * 1.5 : $entry + $atr * 1.5;
        $tp = $buy ? $entry + $atr * 2.5 : $entry - $atr * 2.5;

        return [
            'direction' => $direction,
            'entry' => $entry, 'stop_loss' => $sl, 'take_profit' => $tp,
            'note' => 'HIGH-LOW M15 market entry: '.$reason.' (paper trade)',
        ];
    }

    /** Signal style (day trade / swing): wait for confirmed engine signals. */
    private function openSignalTrades(EaBot $bot, array $config): int
    {
        $takenSignals = $bot->trades()->whereNotNull('signal_id')->pluck('signal_id');
        $openMarkets = $bot->openTrades()->pluck('market_id');
        $slots = min($config['per_run'], $config['max_per_day'] - $bot->positions_today);

        $signals = Signal::with('market')
            ->where('status', 'active')
            ->where('data_status', '!=', 'demo')
            ->where('confidence', '>=', $config['min_confidence'])
            ->where(fn ($q) => $q->whereNull('expires_at')->orWhere('expires_at', '>', now()))
            ->when($config['primary_only'], fn ($q) => $q->where('is_primary', true))
            ->when($bot->market_id, fn ($q) => $q->where('market_id', $bot->market_id))
            ->whereNotIn('id', $takenSignals)
            ->whereNotIn('market_id', $openMarkets)
            ->orderByDesc('is_primary')
            ->orderByDesc('confidence')
            ->take(max(0, $slots))
            ->get();

        $opened = 0;
        foreach ($signals as $signal) {
            $plan = [
                'direction' => $signal->direction,
                'entry' => $signal->entry, 'stop_loss' => $signal->stop_loss, 'take_profit' => $signal->take_profit,
                'note' => strtoupper($bot->mode).' entry via '.$signal->strategy.' ('.$signal->confidence.'% confidence, paper trade)',
            ];
            if ($this->openTrade($bot, $signal->market, $plan, $signal->id)) {
                $opened++;
            }
        }

        if ($opened === 0) {
            $this->note = ($config['primary_only']
                ? 'Waiting for a confirmed ★ PRIMARY signal ≥ '.$config['min_confidence'].'% on a live/delayed feed.'
                : 'No eligible signal ≥ '.$config['min_confidence'].'% on a live/delayed feed yet.')
                .' Run a market scan (php artisan forex:scan or the Signals page refresh) to generate fresh signals.';
        }

        return $opened;
    }

    /** Money management: position size follows the account balance. */
    private function openTrade(EaBot $bot, Market $market, array $plan, ?int $signalId): bool
    {
        $stopDistance = abs($plan['entry'] - $plan['stop_loss']);
        if ($stopDistance <= 0) {
            return false;
        }

        $equity = max(1, $bot->capital + $bot->pnl);
        $riskAmount = round($equity * $bot->risk_pct / 100, 2);
        if ($riskAmount < 0.01) {
            return false; // account is blown — refuse to size a position
        }

        EaBotTrade::create([
            'ea_bot_id' => $bot->id,
            'market_id' => $market->id,
            'signal_id' => $signalId,
            'direction' => $plan['direction'],
            'entry' => round($plan['entry'], 5),
            'stop_loss' => round($plan['stop_loss'], 5),
            'take_profit' => round($plan['take_profit'], 5),
            'units' => round($riskAmount / $stopDistance, 6),
            'risk_amount' => $riskAmount,
            'status' => 'open',
            'note' => $plan['note'],
            'opened_at' => now(),
        ]);

        $bot->positions_today++;
        $bot->last_trade_date = now()->toDateString();

        return true;
    }

    private function ema(array $values, int $period): float
    {
        $values = array_slice($values, -($period * 4));
        $k = 2 / ($period + 1);
        $ema = $values[0];
        foreach ($values as $value) {
            $ema = $value * $k + $ema * (1 - $k);
        }

        return $ema;
    }

    private function atr(array $candles, int $period): float
    {
        $candles = array_slice($candles, -($period + 1));
        $trs = [];
        for ($i = 1; $i < count($candles); $i++) {
            $trs[] = max(
                $candles[$i]['high'] - $candles[$i]['low'],
                abs($candles[$i]['high'] - $candles[$i - 1]['close']),
                abs($candles[$i]['low'] - $candles[$i - 1]['close']),
            );
        }

        return $trs === [] ? 0.0 : array_sum($trs) / count($trs);
    }
}
