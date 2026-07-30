@extends('layouts.app')

@section('title', 'Markets')
@section('subtitle', 'All tracked instruments')

@section('content')
    @foreach ($markets as $category => $group)
        <div class="section-title">{{ ucfirst($category) }}</div>
        <div class="card" style="padding: 8px 4px;">
            <div class="table-wrap">
                <table class="table">
                    <thead>
                    <tr>
                        <th>Symbol</th><th>Name</th><th>Price</th><th>Change</th><th>AI bias</th><th>Confidence</th><th>Signals</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach ($group as $market)
                        <tr>
                            <td class="cell-strong"><a href="{{ route('markets.show', $market) }}">{{ $market->symbol }}</a></td>
                            <td class="muted">{{ $market->name }}</td>
                            <td>{{ number_format($market->price, $market->precision()) }}</td>
                            <td class="{{ $market->change_pct >= 0 ? 'up' : 'down' }}">{{ $market->change_pct >= 0 ? '+' : '' }}{{ number_format($market->change_pct, 2) }}%</td>
                            <td><span class="badge {{ $market->ai_bias === 'bullish' ? 'badge-buy' : ($market->ai_bias === 'bearish' ? 'badge-sell' : 'badge-neutral') }}">{{ ucfirst($market->ai_bias ?? 'n/a') }}</span></td>
                            <td>
                                <span class="conf"><span class="conf-track"><span class="conf-fill" style="width: {{ $market->ai_confidence }}%"></span></span><span class="conf-num">{{ $market->ai_confidence }}%</span></span>
                            </td>
                            <td class="muted">{{ $market->active_signals_count }} active</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endforeach
@endsection
