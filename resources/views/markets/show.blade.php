@extends('layouts.app')

@section('title', $market->symbol)
@section('subtitle', $market->name)

@section('content')
    <div class="page-head">
        <div>
            <div class="price-big">{{ number_format($market->price, $market->precision()) }}</div>
            <div class="stat-sub">
                <span class="{{ $market->change_pct >= 0 ? 'up' : 'down' }}">{{ $market->change_pct >= 0 ? '+' : '' }}{{ number_format($market->change_pct, 2) }}%</span>
                over the last 25 candles · price fetched {{ optional($market->price_fetched_at)?->diffForHumans() ?? 'never' }}
            </div>
        </div>
        <div>
            <span class="badge {{ $market->ai_bias === 'bullish' ? 'badge-buy' : ($market->ai_bias === 'bearish' ? 'badge-sell' : 'badge-neutral') }}">AI: {{ ucfirst($market->ai_bias ?? 'n/a') }} {{ $market->ai_confidence }}%</span>
        </div>
    </div>

    @if(($market->data_status ?? 'demo') === 'demo')
        <div class="feed-warning"><strong>Demo market feed.</strong> Remote price providers failed. This price is not current. Run <code>php artisan forex:feed-check {{ $market->symbol }} --fresh</code> to see the provider error, or configure <code>TWELVEDATA_API_KEY</code>.</div>
    @elseif(($market->data_status ?? '') === 'delayed')
        <div class="feed-notice">Market data from {{ strtoupper($market->data_source) }} may be delayed. Last fetched {{ optional($market->price_fetched_at)?->diffForHumans() }}.</div>
    @endif

    <div class="card chart-card">
        <div class="chart-toolbar">
            <div class="timeframe-switch" role="group" aria-label="Chart timeframe">
                @foreach(['M15','H1','H4','D1'] as $tf)<button class="tf-btn {{ $tf==='H1'?'active':'' }}" data-tf="{{ $tf }}">{{ $tf }}</button>@endforeach
                <span class="feed-label feed-{{ $market->data_status ?? 'demo' }}"><i class="dot {{ in_array($market->data_status, ['live','delayed']) ? 'dot-green' : '' }}"></i>{{ strtoupper($market->data_status ?? 'demo') }} · {{ strtoupper($market->data_source ?? 'demo') }}</span>
            </div>
            <div class="chart-legend">
                <span><span class="legend-swatch" style="background:#72bc8f"></span>Buy trendline / support</span>
                <span><span class="legend-swatch" style="background:#e97366"></span>Sell trendline / resistance</span>
            </div>
        </div>
        <div id="chart"></div>
    </div>

    <div class="grid-main" style="margin-top: 16px;">
        <div class="card">
            <div class="stat-label" style="margin-bottom: 10px;">Signals for {{ $market->symbol }}</div>
            <div class="table-wrap">
                <table class="table">
                    <thead>
                    <tr><th>Strategy</th><th>Side</th><th>Entry</th><th>SL</th><th>TP</th><th>R:R</th><th>Feed</th><th>Status</th><th>Reasoning</th></tr>
                    </thead>
                    <tbody>
                    @forelse ($market->signals as $signal)
                        <tr>
                            <td><span class="badge badge-blue">{{ $signal->strategy }}</span></td>
                            <td><span class="badge {{ $signal->direction === 'buy' ? 'badge-buy' : 'badge-sell' }}">{{ strtoupper($signal->direction) }}</span></td>
                            <td>{{ number_format($signal->entry, $market->precision()) }}</td>
                            <td class="down">{{ number_format($signal->stop_loss, $market->precision()) }}</td>
                            <td class="up">{{ number_format($signal->take_profit, $market->precision()) }}</td>
                            <td>{{ number_format($signal->risk_reward, 1) }}</td>
                            <td><span class="feed-chip feed-{{ $signal->data_status }}">{{ strtoupper($signal->data_status ?? 'unknown') }} · {{ strtoupper($signal->data_source ?? 'unknown') }}</span></td>
                            <td><span class="badge {{ $signal->status === 'active' ? 'badge-blue' : 'badge-neutral' }}">{{ ucfirst($signal->status) }}</span></td>
                            <td class="muted" style="white-space: normal; min-width: 220px;">{{ $signal->note }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="9" class="muted">No signals for this market yet.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="stack">
            <div class="card">
                <div class="stat-label" style="margin-bottom: 8px;">AI analysis</div>
                <p class="ai-summary" style="margin: 0 0 12px;">{{ $market->ai_summary ?? 'Run php artisan forex:scan to generate analysis.' }}</p>
                @if ($market->key_levels)
                    <div class="stat-sub" style="margin-bottom: 6px;">Resistance</div>
                    <div>
                        @foreach ($market->key_levels['resistance'] ?? [] as $level)
                            <span class="tag down">{{ number_format($level, $market->precision()) }}</span>
                        @endforeach
                    </div>
                    <div class="stat-sub" style="margin: 10px 0 6px;">Support</div>
                    <div>
                        @foreach ($market->key_levels['support'] ?? [] as $level)
                            <span class="tag up">{{ number_format($level, $market->precision()) }}</span>
                        @endforeach
                    </div>
                @endif
            </div>
            <div class="card">
                <div class="stat-label" style="margin-bottom: 8px;">Trendlines ({{ $market->trendlines->count() }})</div>
                @forelse ($market->trendlines as $line)
                    <div class="news-meta" style="margin-top: 6px;">
                        <span class="badge {{ $line->direction === 'up' ? 'badge-buy' : ($line->direction === 'down' ? 'badge-sell' : 'badge-neutral') }}">{{ $line->kind === 'trend' ? ($line->direction === 'up' ? 'Buy trendline' : 'Sell trendline') : ucfirst($line->kind) }}</span>
                        <span>{{ number_format($line->start_price, $market->precision()) }} → {{ number_format($line->end_price, $market->precision()) }}</span>
                    </div>
                @empty
                    <p class="muted">No trendlines detected yet.</p>
                @endforelse
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script src="https://unpkg.com/lightweight-charts@4.2.0/dist/lightweight-charts.standalone.production.js"></script>
<script>
(async function () {
    const el = document.getElementById('chart');
    const chart = LightweightCharts.createChart(el, {
        layout: { background: { color: 'transparent' }, textColor: 'rgba(255,255,255,0.65)' },
        grid: {
            vertLines: { color: 'rgba(255,255,255,0.06)' },
            horzLines: { color: 'rgba(255,255,255,0.06)' },
        },
        rightPriceScale: { borderColor: 'rgba(255,255,255,0.14)' },
        timeScale: { borderColor: 'rgba(255,255,255,0.14)', timeVisible: true },
        crosshair: { mode: LightweightCharts.CrosshairMode.Normal },
        autoSize: true,
    });

    const candleSeries = chart.addCandlestickSeries({
        upColor: '#72bc8f', downColor: '#e97366',
        wickUpColor: '#72bc8f', wickDownColor: '#e97366',
        borderVisible: false,
    });

    let lineSeries = [];
    async function loadChart(timeframe) {
      const [candles, trendlines] = await Promise.all([
          fetch('{{ route('api.candles', $market) }}?timeframe=' + timeframe).then(r => r.json()),
          fetch('{{ route('api.trendlines', $market) }}?timeframe=' + timeframe).then(r => r.json()),
      ]);
      candleSeries.setData(candles);
      lineSeries.forEach(s => chart.removeSeries(s)); lineSeries = [];
      for (const line of trendlines) {
        const isBuySide = line.direction === 'up' || line.kind === 'support';
        const series = chart.addLineSeries({
            color: isBuySide ? '#72bc8f' : '#e97366',
            lineWidth: 2,
            lineStyle: line.kind === 'trend' ? LightweightCharts.LineStyle.Solid : LightweightCharts.LineStyle.Dashed,
            priceLineVisible: false,
            lastValueVisible: false,
            crosshairMarkerVisible: false,
        });
        series.setData([
            { time: line.start_time, value: Number(line.start_price) },
            { time: line.end_time, value: Number(line.end_price) },
        ]);
        lineSeries.push(series);
      }
      chart.timeScale().fitContent();
    }
    await loadChart('H1');
    document.querySelectorAll('.tf-btn').forEach(btn => btn.addEventListener('click', async () => {
      document.querySelectorAll('.tf-btn').forEach(b => b.classList.remove('active'));
      btn.classList.add('active'); btn.disabled = true;
      await loadChart(btn.dataset.tf); btn.disabled = false;
    }));
    // Poll the selected timeframe every 30 seconds for a live-like experience.
    setInterval(() => loadChart(document.querySelector('.tf-btn.active').dataset.tf), 30000);
})();
</script>
@endpush
