@extends('layouts.app')

@section('title', 'Signals')
@section('subtitle', 'Entry signals from all strategies')

@section('content')
    <div class="action-row">
        <div class="signal-health"><span class="dot dot-green"></span>Only verified remote feeds generate signals</div>
        <form method="POST" action="{{ route('signals.refresh') }}">@csrf<button class="btn btn-primary" type="submit">Refresh signals</button></form>
    </div>
    @if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
    @if(session('warning'))<div class="feed-warning">{{ session('warning') }}</div>@endif
    <div class="chip-row">
        <a class="chip {{ !request('direction') && !request('strategy') ? 'active' : '' }}" href="{{ route('signals.index') }}">All</a>
        <a class="chip {{ request('direction') === 'buy' ? 'active' : '' }}" href="{{ route('signals.index', ['direction' => 'buy']) }}">Buy</a>
        <a class="chip {{ request('direction') === 'sell' ? 'active' : '' }}" href="{{ route('signals.index', ['direction' => 'sell']) }}">Sell</a>
        <a class="chip {{ request('strategy') === 'SMC' ? 'active' : '' }}" href="{{ route('signals.index', ['strategy' => 'SMC']) }}">SMC</a>
        <a class="chip {{ request('strategy') === 'ICT' ? 'active' : '' }}" href="{{ route('signals.index', ['strategy' => 'ICT']) }}">ICT</a>
        <a class="chip {{ request('strategy') === 'MSNR' ? 'active' : '' }}" href="{{ route('signals.index', ['strategy' => 'MSNR']) }}">MSNR</a>
        <a class="chip {{ request('strategy') === 'TECH' ? 'active' : '' }}" href="{{ route('signals.index', ['strategy' => 'TECH']) }}">TECH</a>
        <a class="chip {{ request('status') === 'expired' ? 'active' : '' }}" href="{{ route('signals.index', ['status' => 'expired']) }}">Expired</a>
    </div>

    <div class="card" style="padding: 8px 4px;">
        <div class="table-wrap">
            <table class="table">
                <thead>
                <tr>
                    <th>Market</th><th>TF</th><th>Strategy</th><th>Side</th><th>Entry</th><th>Stop loss</th><th>Take profit</th><th>R:R</th><th>Feed</th><th>Expires</th><th>Confidence</th><th>Reasoning</th><th>Action</th>
                </tr>
                </thead>
                <tbody>
                @forelse ($signals as $signal)
                    <tr>
                        <td class="cell-strong"><a href="{{ route('markets.show', $signal->market) }}">{{ $signal->market->symbol }}</a></td>
                        <td class="muted">{{ $signal->timeframe }}</td>
                        <td><span class="badge badge-blue">{{ $signal->strategy }}</span>@if($signal->is_primary) <span class="badge badge-orange" title="Primary trade plan for this market">★ PRIMARY</span>@endif</td>
                        <td><span class="badge {{ $signal->direction === 'buy' ? 'badge-buy' : 'badge-sell' }}">{{ strtoupper($signal->direction) }}</span></td>
                        <td>{{ number_format($signal->entry, $signal->market->precision()) }}</td>
                        <td class="down">{{ number_format($signal->stop_loss, $signal->market->precision()) }}</td>
                        <td class="up">{{ number_format($signal->take_profit, $signal->market->precision()) }}</td>
                        <td>{{ number_format($signal->risk_reward, 1) }}</td>
                        <td><span class="feed-chip feed-{{ $signal->data_status }}">{{ strtoupper($signal->data_status ?? 'unknown') }} · {{ strtoupper($signal->data_source ?? 'unknown') }}</span></td>
                        <td class="muted">{{ optional($signal->expires_at)?->diffForHumans() ?? '—' }}</td>
                        <td>
                            <span class="conf"><span class="conf-track"><span class="conf-fill" style="width: {{ $signal->confidence }}%"></span></span><span class="conf-num">{{ $signal->confidence }}%</span></span>
                        </td>
                        <td class="muted" style="white-space: normal; min-width: 200px;">{{ $signal->note }}</td>
                        <td>
                            <button class="btn btn-primary" style="padding: 4px 8px; font-size: 11px;" onclick="navigator.clipboard.writeText('{{ $signal->direction === 'buy' ? 'BUY' : 'SELL' }} {{ $signal->market->symbol }} \nEntry: {{ number_format($signal->entry, $signal->market->precision()) }} \nSL: {{ number_format($signal->stop_loss, $signal->market->precision()) }} \nTP: {{ number_format($signal->take_profit, $signal->market->precision()) }}').then(()=>alert('Copied to clipboard!'))">Copy</button>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="12" class="muted">No signals found — run <code>php artisan forex:scan</code>.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="pager">
        @if ($signals->onFirstPage())
            <span class="disabled">← Previous</span>
        @else
            <a href="{{ $signals->previousPageUrl() }}">← Previous</a>
        @endif
        <span>Page {{ $signals->currentPage() }} of {{ $signals->lastPage() }}</span>
        @if ($signals->hasMorePages())
            <a href="{{ $signals->nextPageUrl() }}">Next →</a>
        @else
            <span class="disabled">Next →</span>
        @endif
    </div>
@endsection
