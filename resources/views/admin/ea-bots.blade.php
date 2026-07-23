@extends('layouts.app')

@section('title', 'EA Bots')

@section('content')
<header class="topbar">
    <h1>EA Bots</h1>
    <div class="topbar-meta">Automated paper-trading robots driven by the signal engine</div>
</header>

@if(session('status'))<div class="feed-notice" style="margin-bottom:12px">{{ session('status') }}</div>@endif

<div class="feed-warning" style="margin-bottom:14px">
    Paper trading only — bots simulate execution against the verified market feed. No real broker orders are placed, and bots refuse to trade or settle on DEMO feed prices.
</div>

<section class="card ea-create">
    <h2>Create a bot</h2>
    <form method="POST" action="{{ route('ea.store') }}">
        @csrf
        <div class="ea-mode-grid">
            @foreach($modes as $key => $mode)
            <label class="ea-mode-card">
                <input type="radio" name="mode" value="{{ $key }}" {{ old('mode', 'daytrade') === $key ? 'checked' : '' }}>
                <div>
                    <strong>{{ $mode['label'] }}</strong>
                    <small class="ea-mode-quota">{{ $mode['timeframe'] }} · up to {{ $mode['max_per_day'] }}/day{{ $mode['style'] === 'fast' ? ' · market entry' : ' · min '.$mode['min_confidence'].'% confidence' }}{{ $mode['primary_only'] ? ' · PRIMARY only' : '' }}</small>
                    <p>{{ $mode['description'] }}</p>
                </div>
            </label>
            @endforeach
        </div>
        <div class="form-grid ea-form-grid">
            <label>Bot name
                <input type="text" name="name" value="{{ old('name') }}" placeholder="Gold Day Trader" required maxlength="60">
                @error('name')<small class="field-error">{{ $message }}</small>@enderror
            </label>
            <label>Market
                <select name="market_id">
                    <option value="">All markets</option>
                    @foreach($markets as $market)
                    <option value="{{ $market->id }}" {{ old('market_id') == $market->id ? 'selected' : '' }}>{{ $market->symbol }} · {{ $market->name }}</option>
                    @endforeach
                </select>
            </label>
            <label>Capital (USD $10 – $5000)
                <input type="number" name="capital" value="{{ old('capital', 100) }}" min="10" max="5000" step="1" required>
                <small class="ea-hint">Small money → small positions · big money → big positions. Sizing follows equity automatically.</small>
                @error('capital')<small class="field-error">{{ $message }}</small>@enderror
            </label>
            <label>Risk per trade (%)
                <input type="number" name="risk_pct" value="{{ old('risk_pct', 2) }}" min="0.25" max="5" step="0.25" required>
                <small class="ea-hint">Risk amount = equity × risk %. Position units = risk ÷ stop distance.</small>
                @error('risk_pct')<small class="field-error">{{ $message }}</small>@enderror
            </label>
        </div>
        <button type="submit" class="btn btn-primary">Create bot</button>
    </form>
</section>

<section class="card" style="margin-top:14px">
    <div class="card-head"><h2>Bots</h2><span class="muted">{{ $bots->count() }} configured</span></div>
    <div class="table-wrap">
        <table>
            <thead><tr><th>Bot</th><th>Mode</th><th>Market</th><th>Capital</th><th>Equity</th><th>Today</th><th>Open</th><th>Trades</th><th>Win rate</th><th>PnL</th><th>Status</th><th>Actions</th></tr></thead>
            <tbody>
            @forelse($bots as $bot)
                @php($mode = $modes[$bot->mode] ?? null)
                <tr>
                    <td class="cell-strong">{{ $bot->name }}<small class="ea-note">{{ $bot->last_note ?? 'Not run yet — press Run now or start the scheduler.' }}</small></td>
                    <td><span class="badge badge-blue">{{ $mode['label'] ?? $bot->mode }}</span></td>
                    <td class="muted">{{ $bot->market?->symbol ?? 'All markets' }}</td>
                    <td>${{ number_format($bot->capital, 0) }} <small class="muted">{{ $bot->tier() }}</small></td>
                    <td class="cell-strong">${{ number_format($bot->equity(), 2) }}</td>
                    <td class="muted">{{ $bot->positions_today }}/{{ $mode['max_per_day'] ?? '-' }}</td>
                    <td class="muted">{{ $bot->open_trades_count }}</td>
                    <td class="muted">{{ $bot->trades }}</td>
                    <td>{{ $bot->winRate() }}%</td>
                    <td class="{{ $bot->pnl >= 0 ? 'up' : 'down' }}">{{ $bot->pnl >= 0 ? '+' : '' }}{{ number_format($bot->pnl, 2) }}</td>
                    <td><span class="badge {{ $bot->status === 'running' ? 'badge-buy' : 'badge-orange' }}">{{ strtoupper($bot->status) }}</span></td>
                    <td class="ea-actions">
                        <form method="POST" action="{{ route('ea.run', $bot) }}">@csrf<button class="btn btn-small">Run now</button></form>
                        <form method="POST" action="{{ route('ea.update', $bot) }}">@csrf @method('PATCH')<input type="hidden" name="status" value="{{ $bot->status === 'running' ? 'paused' : 'running' }}"><button class="btn btn-small">{{ $bot->status === 'running' ? 'Pause' : 'Resume' }}</button></form>
                        <form method="POST" action="{{ route('ea.destroy', $bot) }}" onsubmit="return confirm('Delete this bot and its trade history?')">@csrf @method('DELETE')<button class="btn btn-small btn-danger">Delete</button></form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="12" class="terminal-empty">No bots yet. Pick a mode above and create your first EA bot.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</section>

<section class="card" style="margin-top:14px">
    <div class="card-head"><h2>Recent bot trades</h2><span class="muted">paper execution log</span></div>
    <div class="table-wrap">
        <table>
            <thead><tr><th>Bot</th><th>Market</th><th>Side</th><th>Entry</th><th>SL</th><th>TP</th><th>Units</th><th>Risk</th><th>Status</th><th>PnL</th><th>Opened</th></tr></thead>
            <tbody>
            @forelse($recentTrades as $trade)
                <tr>
                    <td class="muted">{{ $trade->bot?->name }}</td>
                    <td class="cell-strong">{{ $trade->market?->symbol }}</td>
                    <td><span class="badge {{ $trade->direction === 'buy' ? 'badge-buy' : 'badge-sell' }}">{{ strtoupper($trade->direction) }}</span></td>
                    <td>{{ number_format($trade->entry, $trade->market?->precision() ?? 2) }}</td>
                    <td class="down">{{ number_format($trade->stop_loss, $trade->market?->precision() ?? 2) }}</td>
                    <td class="up">{{ number_format($trade->take_profit, $trade->market?->precision() ?? 2) }}</td>
                    <td class="muted">{{ rtrim(rtrim(number_format($trade->units, 4), '0'), '.') }}</td>
                    <td class="muted">${{ number_format($trade->risk_amount, 2) }}</td>
                    <td><span class="badge {{ ['open' => 'badge-blue', 'won' => 'badge-buy', 'lost' => 'badge-sell'][$trade->status] ?? 'badge-orange' }}">{{ strtoupper($trade->status) }}</span></td>
                    <td class="{{ ($trade->pnl ?? 0) >= 0 ? 'up' : 'down' }}">{{ $trade->pnl === null ? '—' : (($trade->pnl >= 0 ? '+' : '').number_format($trade->pnl, 2)) }}</td>
                    <td class="muted">{{ $trade->opened_at->diffForHumans() }}</td>
                </tr>
            @empty
                <tr><td colspan="11" class="terminal-empty">No trades yet. Bots enter as soon as an eligible signal appears on a live/delayed feed.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</section>
@endsection
