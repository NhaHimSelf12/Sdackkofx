@extends('layouts.app')

@section('title', 'Dashboard')
@section('subtitle', 'Last scan ' . optional($markets->max('analyzed_at'))?->diffForHumans())

@section('content')
    <div class="action-row signal-actions">
        <div class="signal-health"><span class="dot dot-green"></span>Signal engine scans verified feeds every 5 minutes</div>
        <form method="POST" action="{{ route('signals.refresh') }}">@csrf<button class="btn btn-primary" type="submit">Refresh market & signals</button></form>
    </div>
    @if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
    @if(session('warning'))<div class="feed-warning">{{ session('warning') }}</div>@endif
    @if($markets->contains(fn($m) => ($m->data_status ?? 'demo') === 'demo'))
        <div class="feed-warning"><strong>Some markets are using DEMO prices.</strong> Run <code>php artisan forex:feed-check --fresh</code>. Do not trade from demo values.</div>
    @endif
    <div class="ticker" aria-hidden="true">
        <div class="ticker-track">
            @foreach ([1, 2] as $pass)
                @foreach ($markets as $market)
                    <a class="ticker-item" href="{{ route('markets.show', $market) }}" tabindex="-1">
                        <span class="ticker-symbol">{{ $market->symbol }}</span>
                        <span>{{ number_format($market->price, $market->precision()) }}</span>
                        <span class="{{ $market->change_pct >= 0 ? 'up' : 'down' }}"><span class="ticker-arrow">{{ $market->change_pct >= 0 ? '▲' : '▼' }}</span> {{ number_format(abs($market->change_pct), 2) }}%</span>
                    </a>
                @endforeach
            @endforeach
        </div>
    </div>

    <div class="grid-stats">
        <div class="card">
            <div class="stat-label">Markets tracked</div>
            <div class="stat-value">{{ $stats['markets'] }}</div>
            <div class="stat-sub">{{ $stats['bullish_markets'] }} bullish · {{ $stats['bearish_markets'] }} bearish</div>
        </div>
        <div class="card">
            <div class="stat-label">Active signals</div>
            <div class="stat-value">{{ $stats['active_signals'] }}</div>
            <div class="stat-sub">across all strategies</div>
        </div>
        <div class="card">
            <div class="stat-label">Buy entries</div>
            <div class="stat-value up">{{ $stats['buy_signals'] }}</div>
            <div class="stat-sub">long opportunities</div>
        </div>
        <div class="card">
            <div class="stat-label">Sell entries</div>
            <div class="stat-value down">{{ $stats['sell_signals'] }}</div>
            <div class="stat-sub">short opportunities</div>
        </div>
    </div>

    <div class="section-title" style="background: linear-gradient(90deg, var(--blue), var(--green)); -webkit-background-clip: text; -webkit-text-fill-color: transparent; font-size: 16px; font-weight: 800; display: inline-block;">Markets</div>
    <div class="market-grid">
        @foreach ($markets as $market)
            <a class="market-card" href="{{ route('markets.show', $market) }}">
                <div class="market-symbol">{{ $market->symbol }}</div>
                <div class="market-name">{{ $market->name }} · <span class="feed-text feed-{{ $market->data_status ?? 'demo' }}">{{ strtoupper($market->data_status ?? 'demo') }}</span></div>
                <div class="market-price">{{ number_format($market->price, $market->precision()) }}</div>
                <div class="stat-sub">
                    <span class="{{ $market->change_pct >= 0 ? 'up' : 'down' }}">{{ $market->change_pct >= 0 ? '+' : '' }}{{ number_format($market->change_pct, 2) }}%</span>
                    · <span class="{{ $market->ai_bias === 'bullish' ? 'up' : ($market->ai_bias === 'bearish' ? 'down' : 'muted') }}">{{ $market->ai_bias ?? 'n/a' }}</span>
                </div>
            </a>
        @endforeach
    </div>

    <div class="section-title" style="background: linear-gradient(90deg, var(--orange), var(--red)); -webkit-background-clip: text; -webkit-text-fill-color: transparent; font-size: 16px; font-weight: 800; display: inline-block; margin-top: 40px;">Overview</div>
    <div class="grid-main">
        <div class="stack">
            <div class="card">
                <div class="stat-label" style="margin-bottom: 10px;">AI market analysis — highest conviction</div>
                @foreach ($markets->sortByDesc('ai_confidence')->take(3) as $market)
                    <div class="ai-item">
                        <div class="ai-body">
                            <div class="ai-head">
                                <a class="cell-strong" href="{{ route('markets.show', $market) }}">{{ $market->symbol }}</a>
                                <span class="badge {{ $market->ai_bias === 'bullish' ? 'badge-buy' : ($market->ai_bias === 'bearish' ? 'badge-sell' : 'badge-neutral') }}">{{ ucfirst($market->ai_bias ?? 'neutral') }}</span>
                                <span class="conf"><span class="conf-track"><span class="conf-fill" style="width: {{ $market->ai_confidence }}%"></span></span><span class="conf-num">{{ $market->ai_confidence }}%</span></span>
                            </div>
                            <div class="ai-summary">{{ $market->ai_summary }}</div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="card">
                <div class="stat-label" style="margin-bottom: 10px;">Top entry signals</div>
                <div class="table-wrap">
                    <table class="table">
                        <thead>
                        <tr>
                            <th>Market</th><th>Strategy</th><th>Side</th><th>Entry</th><th>SL</th><th>TP 1/2/3</th><th>R:R</th><th>Feed</th><th>Conf.</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse ($signals as $signal)
                            <tr>
                                <td class="cell-strong"><a href="{{ route('markets.show', $signal->market) }}">{{ $signal->market->symbol }}</a></td>
                                <td><span class="badge badge-blue">{{ $signal->strategy }}</span>@if($signal->is_primary) <span class="badge badge-orange">★</span>@endif</td>
                                <td><span class="badge {{ $signal->direction === 'buy' ? 'badge-buy' : 'badge-sell' }}">{{ strtoupper($signal->direction) }}</span></td>
                                <td>{{ number_format($signal->entry, $signal->market->precision()) }}</td>
                                <td class="down">{{ number_format($signal->stop_loss, $signal->market->precision()) }}</td>
                                <td class="up">
                                    <small>{{ number_format($signal->tp1, $signal->market->precision()) }}</small><br>
                                    <small>{{ number_format($signal->tp2, $signal->market->precision()) }}</small><br>
                                    <strong>{{ number_format($signal->take_profit, $signal->market->precision()) }}</strong>
                                </td>
                                <td>{{ number_format($signal->risk_reward, 1) }}</td>
                                <td><span class="feed-chip feed-{{ $signal->data_status }}">{{ strtoupper($signal->data_status ?? 'unknown') }} · {{ strtoupper($signal->data_source ?? 'unknown') }}</span></td>
                                <td class="muted">{{ $signal->confidence }}%</td>
                            </tr>
                        @empty
                            <tr><td colspan="9" class="muted">No active signals — run <code>php artisan forex:scan</code>.</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="stat-label" style="margin-bottom: 4px;">News & sentiment</div>
            @forelse ($news as $item)
                <div class="news-item">
                    <div class="news-title">{{ $item->title }}</div>
                    <div class="news-meta">
                        <span class="badge {{ $item->sentiment === 'bullish' ? 'badge-buy' : ($item->sentiment === 'bearish' ? 'badge-sell' : 'badge-neutral') }}">{{ ucfirst($item->sentiment) }}</span>
                        @if ($item->impact === 'high')<span class="badge badge-orange">High impact</span>@endif
                        <span>{{ $item->source }}</span>
                        <span>· {{ optional($item->published_at)?->diffForHumans() }}</span>
                    </div>
                </div>
            @empty
                <p class="muted">No news yet.</p>
            @endforelse
        </div>
    </div>
@endsection
