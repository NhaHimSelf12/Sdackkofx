@extends('layouts.app')

@section('title', 'Strategies')
@section('subtitle', 'Pluggable strategy engine')

@section('content')
    <div class="strategy-grid">
        @foreach ($strategies as $strategy)
            <div class="card">
                <span class="strategy-code">{{ $strategy['code'] }}</span>
                <div class="strategy-name">{{ $strategy['name'] }}</div>
                <div class="strategy-desc">{{ $strategy['description'] }}</div>
                <div style="margin-top: 12px;">
                    @foreach ($strategy['concepts'] as $concept)
                        <span class="tag">{{ $concept }}</span>
                    @endforeach
                </div>
                <div class="strategy-stats">
                    <span><strong>{{ $strategy['active_signals'] }}</strong> active</span>
                    <span class="up"><strong>{{ $strategy['buy'] }}</strong> buy</span>
                    <span class="down"><strong>{{ $strategy['sell'] }}</strong> sell</span>
                </div>
            </div>
        @endforeach
    </div>

    <div class="section-title">Add your own strategy</div>
    <div class="card">
        <p class="ai-summary" style="margin: 0;">
            Create a class in <code>app/Domain/Strategies</code> implementing <code>StrategyInterface</code>,
            then register it in <code>StrategyRegistry::all()</code>. It will automatically appear here and be
            picked up by the signal engine on the next <code>php artisan forex:scan</code>.
        </p>
    </div>
@endsection
