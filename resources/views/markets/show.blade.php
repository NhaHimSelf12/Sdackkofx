@extends('layouts.app')

@section('title', $market->symbol)
@section('subtitle', $market->name)

@section('content')
<div class="space-y-6">

    <!-- Header Stats -->
    <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4">
        <div>
            <div class="num text-4xl font-bold leading-none mb-2">{{ number_format($market->price, $market->precision()) }}</div>
            <div class="flex flex-wrap items-center gap-2 text-[12px] text-[var(--muted)]">
                <span class="num font-semibold {{ $market->change_pct >= 0 ? 'text-[var(--up)]' : 'text-[var(--down)]' }}">{{ $market->change_pct >= 0 ? '+' : '' }}{{ number_format($market->change_pct, 2) }}%</span>
                <span>over the last 25 candles</span>
                <span>·</span>
                <span>price fetched {{ optional($market->price_fetched_at)?->diffForHumans() ?? 'never' }}</span>
            </div>
        </div>
        <div>
            @php $biasClass = $market->ai_bias === 'bullish' ? 'text-[var(--up)] bg-[var(--up)]/12 border-[var(--up)]/20' : ($market->ai_bias === 'bearish' ? 'text-[var(--down)] bg-[var(--down)]/12 border-[var(--down)]/20' : 'text-[var(--muted)] bg-[var(--raised)] border-[var(--line)]'); @endphp
            <span class="px-2.5 py-1 rounded-md border capitalize font-semibold text-[12px] {{ $biasClass }}">AI: {{ ucfirst($market->ai_bias ?? 'n/a') }} {{ $market->ai_confidence }}%</span>
        </div>
    </div>

    <!-- Warnings -->
    @if(($market->data_status ?? 'demo') === 'demo')
        <div class="flex items-start gap-3 rounded-xl border border-[var(--warn)]/25 bg-[var(--warn)]/8 px-4 py-3.5">
            <svg class="w-4 h-4 text-[var(--warn)] mt-0.5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 9v4M12 17h.01M10.3 3.9L1.8 18a2 2 0 001.7 3h17a2 2 0 001.7-3L13.7 3.9a2 2 0 00-3.4 0z"/></svg>
            <p class="text-[13px] leading-relaxed text-[var(--warn)] m-0">
                <span class="font-semibold">Demo market feed.</span> Remote price providers failed. This price is not current. Run <code class="num text-[11px] px-1.5 py-0.5 rounded bg-black/20 border border-[var(--warn)]/20">php artisan forex:feed-check {{ $market->symbol }} --fresh</code> to see the provider error.
            </p>
        </div>
    @elseif(($market->data_status ?? '') === 'delayed')
        <div class="flex items-start gap-3 rounded-xl border border-[var(--warn)]/25 bg-[var(--warn)]/8 px-4 py-3.5">
            <p class="text-[13px] leading-relaxed text-[var(--warn)] m-0">Market data may be delayed. Last fetched {{ optional($market->price_fetched_at)?->diffForHumans() }}.</p>
        </div>
    @endif

    <!-- Chart -->
    <div class="rounded-xl border border-[var(--line)] bg-[var(--surface)] overflow-hidden flex flex-col" style="height: 500px;">
        <div class="h-12 shrink-0 border-b border-[var(--line)] flex items-center justify-between px-4 bg-[var(--base)]/40">
            <div class="flex items-center gap-1" role="group" aria-label="Chart timeframe">
                @foreach(['M15','H1','H4','D1'] as $tf)
                    <button class="tf-btn num text-[11px] font-semibold px-2.5 py-1.5 rounded-md hover:bg-[var(--raised)] transition-colors border-none bg-transparent cursor-pointer text-[var(--muted)] aria-[pressed=true]:text-[var(--text)] aria-[pressed=true]:bg-[var(--raised)] {{ $tf==='H1'?'active-tf':'' }}" data-tf="{{ $tf }}" {{ $tf==='H1'?'aria-pressed=true':'' }}>{{ $tf }}</button>
                @endforeach
                <div class="ml-3 pl-3 border-l border-[var(--line)] flex items-center gap-2">
                    <span class="relative w-1.5 h-1.5 rounded-full {{ in_array($market->data_status, ['live','delayed']) ? 'bg-[var(--up)] pulse' : 'bg-[var(--warn)]' }}"></span>
                    <span class="text-[10px] font-bold tracking-widest uppercase {{ in_array($market->data_status, ['live','delayed']) ? 'text-[var(--up)]' : 'text-[var(--warn)]' }}">{{ strtoupper($market->data_status ?? 'demo') }}{{ ($market->data_source ?? '') === 'yahoo' ? '' : ' · ' . strtoupper($market->data_source ?? 'demo') }}</span>
                </div>
            </div>
            <div class="hidden sm:flex items-center gap-4 text-[11px] text-[var(--muted)]">
                <span class="flex items-center gap-1.5"><span class="w-3 h-1 rounded-full bg-[#72bc8f]"></span>Buy trendline / support</span>
                <span class="flex items-center gap-1.5"><span class="w-3 h-1 rounded-full bg-[#e97366]"></span>Sell trendline / resistance</span>
            </div>
        </div>
        <div id="chart" class="flex-1 min-h-0 w-full relative"></div>
    </div>

    <!-- Details Grid -->
    <div class="grid grid-cols-1 xl:grid-cols-3 gap-5">
        <div class="xl:col-span-2 rounded-xl border border-[var(--line)] bg-[var(--surface)] overflow-hidden min-w-0">
            <div class="px-5 py-4 border-b border-[var(--line)]">
                <h2 class="text-[13px] font-semibold m-0">Signals for {{ $market->symbol }}</h2>
            </div>
            <div class="overflow-x-auto thin-scroll">
                <table class="w-full text-[13px] min-w-[720px] m-0 border-collapse">
                    <thead>
                        <tr class="text-[10px] uppercase tracking-[0.12em] text-[var(--muted)] border-b border-[var(--line)] bg-[var(--base)]/40">
                            <th class="text-left font-medium px-5 py-3 border-none">Strategy</th>
                            <th class="text-left font-medium px-3 py-3 border-none">Side</th>
                            <th class="text-right font-medium px-3 py-3 border-none">Entry</th>
                            <th class="text-right font-medium px-3 py-3 border-none">SL</th>
                            <th class="text-right font-medium px-3 py-3 border-none">TP</th>
                            <th class="text-right font-medium px-3 py-3 border-none">R:R</th>
                            <th class="text-left font-medium px-3 py-3 border-none">Status</th>
                            <th class="text-left font-medium px-5 py-3 border-none">Reasoning</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[var(--line)]">
                        @forelse ($market->signals as $signal)
                        @php $buy = $signal->direction === 'buy'; @endphp
                        <tr class="hover:bg-[var(--raised)]/50 transition-colors">
                            <td class="px-5 py-3.5 border-none"><span class="text-[11px] px-2 py-0.5 rounded border border-[var(--brand)]/25 bg-[var(--brand)]/10 text-[var(--brand)]">{{ $signal->strategy }}</span></td>
                            <td class="px-3 py-3.5 border-none"><span class="text-[11px] font-semibold px-2 py-0.5 rounded border {{ $buy ? 'text-[var(--up)] border-[var(--up)]/25 bg-[var(--up)]/10' : 'text-[var(--down)] border-[var(--down)]/25 bg-[var(--down)]/10' }}">{{ strtoupper($signal->direction) }}</span></td>
                            <td class="num px-3 py-3.5 text-right border-none">{{ number_format($signal->entry, $market->precision()) }}</td>
                            <td class="num px-3 py-3.5 text-right text-[var(--down)] border-none">{{ number_format($signal->stop_loss, $market->precision()) }}</td>
                            <td class="num px-3 py-3.5 text-right text-[var(--up)] border-none font-medium">{{ number_format($signal->take_profit, $market->precision()) }}</td>
                            <td class="num px-3 py-3.5 text-right font-semibold border-none">{{ number_format($signal->risk_reward, 1) }}</td>
                            <td class="px-3 py-3.5 border-none"><span class="text-[10px] px-1.5 py-0.5 rounded border {{ $signal->status === 'active' ? 'text-[var(--brand)] border-[var(--brand)]/25 text-[var(--brand)]' : 'text-[var(--muted)] border-[var(--line)]' }}">{{ ucfirst($signal->status) }}</span></td>
                            <td class="px-5 py-3.5 text-[12px] text-[var(--muted)] border-none min-w-[220px]" style="white-space: normal;">{{ $signal->note }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="8" class="px-5 py-6 text-center text-[var(--muted)] border-none">No signals for this market yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="space-y-5">
            <div class="rounded-xl border border-[var(--line)] bg-[var(--surface)] p-5">
                <h2 class="text-[13px] font-semibold mb-3 m-0">AI analysis</h2>
                <p class="text-[13px] leading-relaxed text-[var(--text)]/80 m-0 mb-4">{{ $market->ai_summary ?? 'Run php artisan forex:scan to generate analysis.' }}</p>
                @if ($market->key_levels)
                    <h3 class="text-[11px] font-medium uppercase tracking-[0.14em] text-[var(--muted)] mb-2.5 m-0">Resistance</h3>
                    <div class="flex flex-wrap gap-1.5 mb-4">
                        @foreach ($market->key_levels['resistance'] ?? [] as $level)
                            <span class="num text-[11px] px-2 py-0.5 rounded border border-[var(--down)]/20 bg-[var(--down)]/10 text-[var(--down)]">{{ number_format($level, $market->precision()) }}</span>
                        @endforeach
                    </div>
                    <h3 class="text-[11px] font-medium uppercase tracking-[0.14em] text-[var(--muted)] mb-2.5 m-0">Support</h3>
                    <div class="flex flex-wrap gap-1.5">
                        @foreach ($market->key_levels['support'] ?? [] as $level)
                            <span class="num text-[11px] px-2 py-0.5 rounded border border-[var(--up)]/20 bg-[var(--up)]/10 text-[var(--up)]">{{ number_format($level, $market->precision()) }}</span>
                        @endforeach
                    </div>
                @endif
            </div>

            <div class="rounded-xl border border-[var(--line)] bg-[var(--surface)] p-5">
                <h2 class="text-[13px] font-semibold mb-3 m-0">Trendlines ({{ $market->trendlines->count() }})</h2>
                <div class="space-y-2.5">
                    @forelse ($market->trendlines as $line)
                        <div class="flex items-center gap-2 text-[12px]">
                            @php $bClass = $line->direction === 'up' ? 'text-[var(--up)] border-[var(--up)]/20 bg-[var(--up)]/10' : ($line->direction === 'down' ? 'text-[var(--down)] border-[var(--down)]/20 bg-[var(--down)]/10' : 'text-[var(--muted)] border-[var(--line)] bg-[var(--raised)]'); @endphp
                            <span class="px-1.5 py-0.5 rounded border {{ $bClass }} text-[10px]">{{ $line->kind === 'trend' ? ($line->direction === 'up' ? 'Buy trendline' : 'Sell trendline') : ucfirst($line->kind) }}</span>
                            <span class="num text-[var(--muted)]">{{ number_format($line->start_price, $market->precision()) }} → {{ number_format($line->end_price, $market->precision()) }}</span>
                        </div>
                    @empty
                        <p class="text-[12px] text-[var(--muted)] m-0">No trendlines detected yet.</p>
                    @endforelse
                </div>
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
    if(!el) return;
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
          fetch('{{ route('api.candles', $market, false) }}?timeframe=' + timeframe).then(r => r.json()),
          fetch('{{ route('api.trendlines', $market, false) }}?timeframe=' + timeframe).then(r => r.json()),
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
      document.querySelectorAll('.tf-btn').forEach(b => {
          b.classList.remove('active-tf');
          b.setAttribute('aria-pressed', 'false');
      });
      btn.classList.add('active-tf'); 
      btn.setAttribute('aria-pressed', 'true');
      btn.disabled = true;
      await loadChart(btn.dataset.tf); btn.disabled = false;
    }));
    // Poll the selected timeframe every 30 seconds for a live-like experience.
    setInterval(() => loadChart(document.querySelector('.tf-btn.active-tf').dataset.tf), 30000);
})();
</script>
@endpush
