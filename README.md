# FX Command — Forex Management System (Laravel 12)

A forex management system built with **Laravel 12** (also works on Laravel 11) featuring:

- **AI market analysis** — per-market bias, confidence score and key levels. Uses OpenAI when an API key is set, otherwise falls back to a built-in technical engine (EMA trend, RSI, market structure).
- **News analysis** — financial news feed with sentiment (bullish / bearish / neutral) and impact scoring per symbol.
- **Strategy engine** — pluggable strategies: **SMC** (Smart Money Concepts: BOS/CHoCH, order blocks, liquidity sweeps), **ICT** (fair value gaps, kill zones, premium/discount), **MSNR** (market structure + support/resistance). Add your own by implementing `StrategyInterface`.
- **Verified-feed entry signals** — buy/sell signals with entry, stop-loss, take-profit, risk:reward, confidence, market-data source, generation time and expiry. Demo feeds are blocked from signal generation.
- **All markets watchlist** — XAUUSD, BTCUSD, EURUSD, GBPUSD, USDJPY, ETHUSD, US30 and more (add any symbol).
- **V3 trading terminal** — dependency-free interactive M1/M5/M15/H1/H4/D1 candlestick canvas, five-second refresh, candle countdown, OHLC crosshair, volume, FVG zones, volume profile, automatic trendlines and buy/sell markers.
- **Accounts & admin** — secure login/register, trader/admin roles and an admin control center.
- **Trading journal** — trade planning, performance history, win rate, net P/L and average R.
- **Risk calculator** — balance/risk/stop-loss based lot-size estimation.
- **Personal watchlists** — each user can save preferred markets.

---

## Requirements

- PHP 8.2+ with `sqlite3` extension (or MySQL)
- Composer

## Installation

```bash
# 1. Unzip and enter the project
cd forex-management-system

# 2. Install Laravel 12 dependencies
composer install

# 3. Environment and SQLite
cp .env.example .env
php artisan key:generate
touch database/database.sqlite

# 4. Create all tables + demo admin, markets, strategies, news and signals
php artisan migrate --seed

# 5. Run a fresh market scan
php artisan forex:feed-check --fresh   # confirm prices are LIVE/DELAYED, not DEMO
php artisan forex:scan

# 6. Start the website
php artisan serve
```

Open http://localhost:8000 for the public frontend. User login opens the protected dashboard and Trading Terminal.

Log in with:

- **Admin email:** `admin@fxcommand.test`
- **Password:** `password`

Change this password before deployment. New traders can also use the Register page.

## Troubleshooting

**“Your requirements could not be resolved… affected by security advisories (PKSA-…)”**

Your Composer blocks package versions that have known security advisories, and all plain
Laravel 11 releases have advisories filed against them. Two ways to fix it:

1. **Recommended:** use Laravel 12 — this project's `composer.json` already requires
   `laravel/framework: ^12.0`, so just run `composer update` (or create the fresh app with
   `composer create-project laravel/laravel:^12.0`). Make sure you are on PHP 8.2+.
2. If you must stay on Laravel 11, allow insecure versions in `composer.json`:

```json
"config": {
    "audit": {
        "block-insecure": false
    }
}
```

Then run `composer update` again.

## Optional API keys (`.env`)

| Key | Purpose | Fallback when empty |
|---|---|---|
| `OPENAI_API_KEY` | LLM-written market analysis | Built-in technical engine |
| `TWELVEDATA_API_KEY` | Live OHLC candles & prices | Yahoo delayed feed, then clearly-labelled demo fallback |
| `NEWSAPI_KEY` | Live financial news | Seeded demo news |

## Useful commands

```bash
php artisan forex:feed-check --fresh # show actual price source/status/errors
php artisan forex:scan            # generate signals from verified feeds
php artisan forex:signal-check    # inspect active signal source and expiry
php artisan forex:scan XAUUSD     # scan a single symbol
php artisan db:seed               # reseed demo data
```

## Project structure

```
app/
  Console/Commands/ScanMarkets.php      # forex:scan
  Domain/Strategies/                    # SMC, ICT, MSNR + StrategyInterface
  Http/Controllers/                     # Dashboard, Markets, Signals, News, Strategies + API
  Models/                               # Market, Signal, NewsItem, Strategy, Trendline
  Services/                             # AiMarketAnalysisService, NewsAnalysisService,
                                        # MarketDataService, SignalEngine, TrendlineDetector
config/forex.php
database/migrations/                    # markets, signals, news_items, strategies, trendlines
database/seeders/
resources/views/                        # Blade UI (dark trading theme)
routes/web.php  routes/api.php
public/css/app.css
```

## Adding a strategy

Create a class in `app/Domain/Strategies` implementing `StrategyInterface`, then register it in `StrategyRegistry::all()`. It will automatically appear on the Strategies page and be used by the signal engine.

## Market feed status

- **LIVE / TWELVEDATA:** API-key feed.
- **DELAYED / YAHOO:** public feed, normally delayed; XAUUSD uses COMEX Gold futures (`GC=F`) as a transparent proxy.
- **DEMO:** both remote providers failed. The UI displays a prominent warning and the value must not be used for trading.

Always run `php artisan forex:feed-check --fresh` after installation and before trusting displayed prices.

## Trading Terminal data behavior

The terminal requests a fresh candle snapshot every five seconds and updates without a page reload. M1/M5 candles require a provider that supports intraday history. This creates a TradingView-like experience, but true tick-by-tick WebSocket streaming for XAUUSD/Forex still requires a broker or Twelve Data WebSocket subscription. The UI never labels Yahoo delayed data as live.

Included terminal overlays:

- Bullish and bearish FVG zones
- Buy-side and sell-side swing trendlines
- Candle volume and price-binned volume profile
- Active BUY/SELL entry markers
- Exact countdown to the next M1/M5/M15/H1/H4/D1 candle
- Feed status and explicit DEMO warning

## One clear decision per market (V4)

Every scan marks exactly one **★ PRIMARY** signal per market: the highest-confidence signal that agrees with the strategy majority, boosted by confluence. The Trading Terminal shows it as a “Primary trade plan” card (direction, entry, SL, TP, R:R, confidence, validity time) plus a market-analysis checklist (Trend / Momentum / Structure / Key levels) with a plain-language verdict. Other entries are listed as supporting confluence only.

## EA Bots (V5, admin only)

Admins get an **EA Bots** page in the sidebar to run automated paper-trading robots on top of the signal engine. Four modes:

| Mode | Pace | Timeframe focus | Entry rule |
| --- | --- | --- | --- |
| Scalping | up to 20 positions/day | M1 | SMC style: buys discount pullbacks at swing lows, sells premium rallies at swing highs — never chases breakouts |
| High & Low | 5–6 positions/day | M15 | Market entry on breaks/rejections of the session high and low |
| Day Trade | 2–3 positions/day | H1 | Waits for a confirmed ★ PRIMARY setup, min 72% |
| Swing Trade | 1–2 positions/day | H4 | Highest conviction ★ PRIMARY only, min 78% |

Money management follows the account balance ($10–$5000): risk per trade = equity × risk % (0.25–5%), position units = risk ÷ stop distance — small accounts trade small, big accounts trade proportionally bigger. Bots track equity, daily quota, win rate and PnL, and can be paused, resumed, run on demand or deleted.

Scalping and High & Low bots read live M1/M15 candles directly and enter **at the current market price** the moment a setup appears — they do not wait for the slower H1 signal scan. Day Trade and Swing bots deliberately wait for confirmed ★ PRIMARY signals.

**Honest by design:** bots are paper-trading only. They never place real broker orders, refuse to enter on DEMO feed data, and never settle trades against demo prices. Scheduling runs them every minute via `php artisan schedule:work` (or the production cron), and you can trigger them manually with `php artisan forex:ea-run`.

## Dark & light mode (V4)

Every page has a theme toggle (sidebar, public navbar and auth pages). The choice is saved in the browser, follows the OS preference by default, and the terminal chart re-renders with theme-matched colors.

## Signal automation

Signals are generated from SMC, ICT, MSNR and TECH technical-confluence models. The engine refuses to generate signals when a market is on the DEMO feed. To run automatic five-minute scans:

```bash
# Local development
php artisan schedule:work

# Production cron (run every minute)
* * * * * cd /path/to/forex-management-system && php artisan schedule:run >> /dev/null 2>&1
```

You can also press **Refresh signals** in the dashboard or Signals page. Verify results with `php artisan forex:signal-check`.

> **Disclaimer:** signals and analysis are for information/education only and are not financial advice.
