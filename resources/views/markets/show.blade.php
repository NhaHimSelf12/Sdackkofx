@extends('layouts.app')

@section('title', $market->symbol)
@section('subtitle', $market->name)

@push('styles')
<style>
  .chart-card { min-height: 400px; display: flex; flex-direction: column; }
  .chart-toolbar { display: flex; justify-content: space-between; align-items: center; padding: 1rem; border-bottom: 1px solid rgba(0,0,0,0.1); }
  .timeframe-switch button { background: transparent; border: 1px solid #d9dee3; padding: 4px 12px; border-radius: 4px; color: #697a8d; cursor: pointer; transition: all 0.2s; }
  .timeframe-switch button.active { background: #696cff; color: #fff; border-color: #696cff; }
  .chart-legend span { display: inline-flex; align-items: center; font-size: 12px; color: #a1acb8; margin-left: 12px; }
  .legend-swatch { width: 12px; height: 12px; border-radius: 2px; margin-right: 6px; }
  #chart { flex: 1; min-height: 350px; }
</style>
@endpush

@section('content')
<div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4">
    <div>
        <h4 class="fw-bold mb-1"><span class="text-muted fw-light">Markets /</span> {{ $market->symbol }}</h4>
        <div class="d-flex align-items-center gap-2 mt-2">
            <h3 class="mb-0 fw-bold">{{ number_format($market->price, $market->precision()) }}</h3>
            <span class="text-{{ $market->change_pct >= 0 ? 'success' : 'danger' }} fw-semibold fs-5">
                <i class="bx bx-{{ $market->change_pct >= 0 ? 'up' : 'down' }}-arrow-alt"></i>
                {{ number_format(abs($market->change_pct), 2) }}%
            </span>
        </div>
        <small class="text-muted mt-1 d-block">Over the last 25 candles &middot; Price fetched {{ optional($market->price_fetched_at)?->diffForHumans() ?? 'never' }}</small>
    </div>
    <div class="mt-3 mt-md-0">
        <span class="badge bg-label-{{ $market->ai_bias === 'bullish' ? 'success' : ($market->ai_bias === 'bearish' ? 'danger' : 'secondary') }} fs-6 p-2">
            <i class="bx bx-bot me-1"></i> AI Bias: {{ ucfirst($market->ai_bias ?? 'n/a') }} ({{ $market->ai_confidence }}%)
        </span>
    </div>
</div>

@if(($market->data_status ?? 'demo') === 'demo')
<div class="alert alert-danger alert-dismissible" role="alert">
  <h6 class="alert-heading fw-bold mb-1"><i class="bx bx-error-circle me-1"></i> Demo market feed</h6>
  <p class="mb-0">Remote price providers failed. This price is not current. Run <code>php artisan forex:feed-check {{ $market->symbol }} --fresh</code> to see the provider error, or configure <code>TWELVEDATA_API_KEY</code>.</p>
  <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@elseif(($market->data_status ?? '') === 'delayed')
<div class="alert alert-warning alert-dismissible" role="alert">
  <p class="mb-0"><i class="bx bx-time me-1"></i> Market data may be delayed. Last fetched {{ optional($market->price_fetched_at)?->diffForHumans() }}.</p>
  <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

<div class="row mb-4">
    <div class="col-12">
        <div class="card chart-card">
            <div class="chart-toolbar flex-wrap gap-2">
                <div class="d-flex align-items-center gap-3">
                    <div class="timeframe-switch d-flex gap-1" role="group" aria-label="Chart timeframe">
                        @foreach(['M15','H1','H4','D1'] as $tf)
                        <button class="tf-btn {{ $tf==='H1'?'active':'' }}" data-tf="{{ $tf }}">{{ $tf }}</button>
                        @endforeach
                    </div>
                    <span class="feed-chip feed-{{ $market->data_status ?? 'demo' }}">{{ strtoupper($market->data_status ?? 'demo') }}{{ ($market->data_source ?? '') === 'yahoo' ? '' : ' · ' . strtoupper($market->data_source ?? 'demo') }}</span>
                </div>
                <div class="chart-legend">
                    <span><span class="legend-swatch" style="background:#71dd37"></span>Buy trendline / support</span>
                    <span><span class="legend-swatch" style="background:#ff3e1d"></span>Sell trendline / resistance</span>
                </div>
            </div>
            <div id="chart"></div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-xl-8 col-lg-7 col-12 mb-4">
        <div class="card">
            <h5 class="card-header">Signals for {{ $market->symbol }}</h5>
            <div class="table-responsive text-nowrap">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Strategy</th>
                            <th>Side</th>
                            <th>Entry</th>
                            <th>SL</th>
                            <th>TP 1/2/3</th>
                            <th>R:R</th>
                            <th>Feed</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody class="table-border-bottom-0">
                        @forelse ($market->signals as $signal)
                        <tr>
                            <td><span class="badge bg-label-primary">{{ $signal->strategy }}</span></td>
                            <td><span class="badge bg-label-{{ $signal->direction === 'buy' ? 'success' : 'danger' }}">{{ strtoupper($signal->direction) }}</span></td>
                            <td>{{ number_format($signal->entry, $market->precision()) }}</td>
                            <td class="text-danger">{{ number_format($signal->stop_loss, $market->precision()) }}</td>
                            <td class="text-success">
                                <small class="text-muted d-block">{{ number_format($signal->tp1, $market->precision()) }}</small>
                                <small class="text-muted d-block">{{ number_format($signal->tp2, $market->precision()) }}</small>
                                <strong class="d-block">{{ number_format($signal->take_profit, $market->precision()) }}</strong>
                            </td>
                            <td>{{ number_format($signal->risk_reward, 1) }}</td>
                            <td><span class="feed-chip feed-{{ $signal->data_status }}">{{ strtoupper($signal->data_status ?? 'unknown') }}{{ ($signal->data_source ?? '') === 'yahoo' ? '' : ' · ' . strtoupper($signal->data_source ?? 'unknown') }}</span></td>
                            <td>
                                <span class="badge bg-label-{{ $signal->status === 'active' ? 'primary' : 'secondary' }}">{{ ucfirst($signal->status) }}</span>
                            </td>
                        </tr>
                        @if($signal->note)
                        <tr>
                            <td colspan="8" class="text-muted border-bottom-0 py-2"><i class="bx bx-info-circle me-1"></i> {{ $signal->note }}</td>
                        </tr>
                        @endif
                        @empty
                        <tr><td colspan="8" class="text-center text-muted">No signals for this market yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-xl-4 col-lg-5 col-12 mb-4">
        <div class="card mb-4">
            <h5 class="card-header">AI Analysis</h5>
            <div class="card-body">
                <p class="mb-4">{{ $market->ai_summary ?? 'Run php artisan forex:scan to generate analysis.' }}</p>
                
                @if ($market->key_levels)
                <h6 class="text-muted mb-2">Resistance Levels</h6>
                <div class="d-flex flex-wrap gap-2 mb-4">
                    @foreach ($market->key_levels['resistance'] ?? [] as $level)
                        <span class="badge bg-label-danger">{{ number_format($level, $market->precision()) }}</span>
                    @endforeach
                </div>
                
                <h6 class="text-muted mb-2">Support Levels</h6>
                <div class="d-flex flex-wrap gap-2">
                    @foreach ($market->key_levels['support'] ?? [] as $level)
                        <span class="badge bg-label-success">{{ number_format($level, $market->precision()) }}</span>
                    @endforeach
                </div>
                @endif
            </div>
        </div>
        
        <div class="card">
            <h5 class="card-header d-flex justify-content-between align-items-center">
                Trendlines
                <span class="badge bg-primary rounded-pill">{{ $market->trendlines->count() }}</span>
            </h5>
            <div class="card-body">
                <ul class="p-0 m-0">
                    @forelse ($market->trendlines as $line)
                    <li class="d-flex mb-3">
                        <div class="avatar flex-shrink-0 me-3">
                            <span class="avatar-initial rounded bg-label-{{ $line->direction === 'up' ? 'success' : ($line->direction === 'down' ? 'danger' : 'secondary') }}">
                                <i class="bx bx-trending-{{ $line->direction === 'up' ? 'up' : 'down' }}"></i>
                            </span>
                        </div>
                        <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                            <div class="me-2">
                                <h6 class="mb-1">{{ $line->kind === 'trend' ? ($line->direction === 'up' ? 'Buy trendline' : 'Sell trendline') : ucfirst($line->kind) }}</h6>
                                <small class="text-muted d-block">
                                    {{ number_format($line->start_price, $market->precision()) }} &rarr; {{ number_format($line->end_price, $market->precision()) }}
                                </small>
                            </div>
                        </div>
                    </li>
                    @empty
                    <li class="text-center text-muted">No trendlines detected yet.</li>
                    @endforelse
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://unpkg.com/lightweight-charts@4.2.0/dist/lightweight-charts.standalone.production.js"></script>
<script>
(async function () {
    const el = document.getElementById('chart');
    const isDark = document.documentElement.classList.contains('dark-style');
    const chart = LightweightCharts.createChart(el, {
        layout: { background: { color: 'transparent' }, textColor: isDark ? '#a3a4cc' : '#566a7f' },
        grid: {
            vertLines: { color: isDark ? 'rgba(255,255,255,0.05)' : 'rgba(0,0,0,0.05)' },
            horzLines: { color: isDark ? 'rgba(255,255,255,0.05)' : 'rgba(0,0,0,0.05)' },
        },
        rightPriceScale: { borderColor: isDark ? 'rgba(255,255,255,0.1)' : 'rgba(0,0,0,0.1)' },
        timeScale: { borderColor: isDark ? 'rgba(255,255,255,0.1)' : 'rgba(0,0,0,0.1)', timeVisible: true },
        crosshair: { mode: LightweightCharts.CrosshairMode.Normal },
        autoSize: true,
    });

    const candleSeries = chart.addCandlestickSeries({
        upColor: '#71dd37', downColor: '#ff3e1d',
        wickUpColor: '#71dd37', wickDownColor: '#ff3e1d',
        borderVisible: false,
    });

    let lineSeries = [];
    async function loadChart(timeframe) {
      const [candles, trendlines] = await Promise.all([
          fetch('{{ route('api.candles', $market, false) }}?timeframe=' + timeframe).then(r => r.json()),
          fetch('{{ route('api.trendlines', $market, false) }}?timeframe=' + timeframe).then(r => r.json()),
      ]);
      candleSeries.setData(candles);
      lineSeries.forEach(s => chart.removeSeries(s)); lineSeries = [];
      for (const line of trendlines) {
        const isBuySide = line.direction === 'up' || line.kind === 'support';
        const series = chart.addLineSeries({
            color: isBuySide ? '#71dd37' : '#ff3e1d',
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
