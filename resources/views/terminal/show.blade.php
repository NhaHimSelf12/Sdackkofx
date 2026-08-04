@extends('layouts.app')

@section('title', 'Trading Terminal')
@section('subtitle', 'Live candles, FVG, volume profile, trendlines and entries')

@push('styles')
<style>
  /* legacy hooks kept for terminal.js */
  .up   { color: var(--up); }
  .down { color: var(--down); }

  .tf-button.active { background:var(--brand); color:#fff; border-color:var(--brand); }

  .feed-chip.feed-demo { color:var(--warn); border-color:color-mix(in srgb,var(--warn) 30%,transparent); background:color-mix(in srgb,var(--warn) 12%,transparent); }
  .feed-chip.feed-live { color:var(--up);   border-color:color-mix(in srgb,var(--up) 30%,transparent);   background:color-mix(in srgb,var(--up) 12%,transparent); }

  .layer-toggle input:checked + .layer-box { background:var(--brand); border-color:var(--brand); }
  .layer-toggle input:checked + .layer-box svg { opacity:1; }
  .layer-toggle input:focus-visible + .layer-box { outline:2px solid var(--brand); outline-offset:2px; }

  .terminal-empty { padding:1.5rem 0; text-align:center; font-size:12px; color:var(--muted); }
</style>
@endpush

@section('content')
<div id="terminal" class="terminal space-y-4" data-endpoint="{{ route('terminal.data', $market) }}" data-symbol="{{ $market->symbol }}">

  <!-- ================= TOP BAR ================= -->
  <div class="terminal-top rounded-xl border border-[var(--line)] bg-[var(--surface)] p-3 flex flex-col md:flex-row flex-wrap items-stretch md:items-center gap-3">

    <div class="terminal-market flex items-center justify-between md:justify-start gap-3 min-w-0 w-full md:w-auto">
      <div class="relative flex-1 md:flex-none">
        <select id="marketSelect" aria-label="Market" class="terminal-select appearance-none w-full h-10 pl-3 pr-9 rounded-lg border border-[var(--line)] bg-[var(--raised)] text-[13px] font-medium text-[var(--text)] hover:border-[var(--brand)]/40 focus:outline-none focus:ring-2 focus:ring-[var(--brand)]/40 transition cursor-pointer">
            @foreach($markets as $m)
                <option value="{{ route('terminal.show', $m) }}" @selected($m->id === $market->id)>{{ $m->symbol }} · {{ $m->name }}</option>
            @endforeach
        </select>
        <svg class="w-3.5 h-3.5 absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none text-[var(--muted)]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 9l6 6 6-6"/></svg>
      </div>

      <div class="flex items-baseline gap-2 shrink-0">
        <strong id="livePrice" class="num text-[18px] md:text-[22px] font-bold leading-none tracking-tight">{{ number_format($market->price, $market->precision()) }}</strong>
        <span id="liveChange" class="{{ $market->change_pct >= 0 ? 'up' : 'down' }} num text-[11px] md:text-[12px] font-semibold px-1.5 py-0.5 rounded">{{ $market->change_pct >= 0 ? '+' : '' }}{{ number_format($market->change_pct, 2) }}%</span>
      </div>
    </div>

    <div class="tf-group flex items-center justify-between md:justify-start gap-1 p-1 rounded-lg bg-[var(--raised)] border border-[var(--line)] w-full md:w-auto overflow-x-auto thin-scroll" role="group" aria-label="Timeframe">
      @foreach(['M1','M5','M15','H1','H4','D1'] as $tf)
          <button class="tf-button {{ $tf === 'M15' ? 'active' : '' }} num h-8 px-3 rounded-md border border-transparent text-[12px] font-semibold text-[var(--muted)] hover:text-[var(--text)] transition shrink-0" data-timeframe="{{ $tf }}">{{ $tf }}</button>
      @endforeach
    </div>

    <div class="flex items-center justify-between md:justify-end gap-3 w-full md:w-auto md:ml-auto">
        <div class="candle-clock flex items-center gap-2.5 h-10 px-3.5 rounded-lg border border-[var(--line)] bg-[var(--raised)] flex-1 md:flex-none justify-center">
          <svg class="w-3.5 h-3.5 text-[var(--muted)]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/></svg>
          <span class="text-[11px] text-[var(--muted)] hidden sm:inline">Next candle</span>
          <strong id="candleCountdown" class="num text-[13px] font-semibold">--:--</strong>
        </div>

        <div id="feedBadge" class="feed-chip feed-{{ strtolower($market->data_status) }} h-10 inline-flex items-center gap-2 px-3.5 rounded-lg border text-[11px] font-semibold tracking-wider uppercase flex-1 md:flex-none justify-center">
          <span class="relative w-1.5 h-1.5 rounded-full bg-current pulse"></span>
          <span class="hidden sm:inline">{{ $market->data_status }}{{ $market->data_source === 'yahoo' ? '' : ' · ' . $market->data_source }}</span>
          <span class="sm:hidden">{{ $market->data_status }}</span>
        </div>
    </div>
  </div>

  <!-- ================= INDICATOR BAR ================= -->
  <div class="indicator-bar rounded-xl border border-[var(--line)] bg-[var(--surface)] px-4 py-3 flex flex-nowrap md:flex-wrap items-center gap-x-5 gap-y-2.5 overflow-x-auto thin-scroll">
    <span class="text-[10px] font-semibold uppercase tracking-[0.16em] text-[var(--muted)] shrink-0">Indicators</span>

    <label class="layer-toggle inline-flex items-center gap-2 cursor-pointer select-none group shrink-0">
      <input type="checkbox" data-layer="fvg" checked class="sr-only">
      <span class="layer-box w-4 h-4 rounded border border-[var(--line)] bg-[var(--raised)] grid place-items-center transition">
        <svg class="w-2.5 h-2.5 text-white opacity-0 transition" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3.5" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6L9 17l-5-5"/></svg>
      </span>
      <span class="text-[12px] text-[var(--muted)] group-hover:text-[var(--text)] transition">FVG zones</span>
    </label>

    <label class="layer-toggle inline-flex items-center gap-2 cursor-pointer select-none group shrink-0">
      <input type="checkbox" data-layer="volume" checked class="sr-only">
      <span class="layer-box w-4 h-4 rounded border border-[var(--line)] bg-[var(--raised)] grid place-items-center transition">
        <svg class="w-2.5 h-2.5 text-white opacity-0 transition" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3.5" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6L9 17l-5-5"/></svg>
      </span>
      <span class="text-[12px] text-[var(--muted)] group-hover:text-[var(--text)] transition">Volume</span>
    </label>

    <label class="layer-toggle inline-flex items-center gap-2 cursor-pointer select-none group shrink-0">
      <input type="checkbox" data-layer="profile" checked class="sr-only">
      <span class="layer-box w-4 h-4 rounded border border-[var(--line)] bg-[var(--raised)] grid place-items-center transition">
        <svg class="w-2.5 h-2.5 text-white opacity-0 transition" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3.5" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6L9 17l-5-5"/></svg>
      </span>
      <span class="text-[12px] text-[var(--muted)] group-hover:text-[var(--text)] transition">Volume profile</span>
    </label>

    <label class="layer-toggle inline-flex items-center gap-2 cursor-pointer select-none group shrink-0">
      <input type="checkbox" data-layer="trendlines" checked class="sr-only">
      <span class="layer-box w-4 h-4 rounded border border-[var(--line)] bg-[var(--raised)] grid place-items-center transition">
        <svg class="w-2.5 h-2.5 text-white opacity-0 transition" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3.5" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6L9 17l-5-5"/></svg>
      </span>
      <span class="text-[12px] text-[var(--muted)] group-hover:text-[var(--text)] transition">Trendlines</span>
    </label>

    <label class="layer-toggle inline-flex items-center gap-2 cursor-pointer select-none group shrink-0">
      <input type="checkbox" data-layer="signals" checked class="sr-only">
      <span class="layer-box w-4 h-4 rounded border border-[var(--line)] bg-[var(--raised)] grid place-items-center transition">
        <svg class="w-2.5 h-2.5 text-white opacity-0 transition" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3.5" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6L9 17l-5-5"/></svg>
      </span>
      <span class="text-[12px] text-[var(--muted)] group-hover:text-[var(--text)] transition">Buy/Sell entries</span>
    </label>

    <label class="layer-toggle inline-flex items-center gap-2 cursor-pointer select-none group shrink-0">
      <input type="checkbox" data-layer="bots" checked class="sr-only">
      <span class="layer-box w-4 h-4 rounded border border-[var(--line)] bg-[var(--raised)] grid place-items-center transition">
        <svg class="w-2.5 h-2.5 text-white opacity-0 transition" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3.5" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6L9 17l-5-5"/></svg>
      </span>
      <span class="text-[12px] text-[var(--muted)] group-hover:text-[var(--text)] transition">Bot entries</span>
    </label>

    <span id="terminalStatus" class="terminal-status md:ml-auto inline-flex items-center gap-2 text-[11px] text-[var(--muted)] shrink-0 pr-2">
      <span class="relative w-1.5 h-1.5 rounded-full bg-[var(--up)] text-[var(--up)] pulse"></span>
      Connected · streaming
    </span>
  </div>

  <!-- ================= GRID ================= -->
  <div class="terminal-grid grid grid-cols-1 xl:grid-cols-[minmax(0,1fr)_380px] gap-4 items-start">

    <!-- ---------- CHART ---------- -->
    <section class="terminal-chart rounded-xl border border-[var(--line)] bg-[var(--surface)] overflow-hidden flex flex-col h-[400px] md:h-[560px] xl:h-[calc(100vh-260px)] xl:min-h-[560px] xl:sticky xl:top-4">
      <div class="shrink-0 flex items-center justify-between gap-3 px-4 h-11 border-b border-[var(--line)]">
        <div class="flex items-center gap-2">
          <span class="num text-[12px] font-semibold">{{ $market->symbol }}</span>
          <span class="text-[10px] px-1.5 py-0.5 rounded border border-[var(--line)] bg-[var(--raised)] text-[var(--muted)]" id="chartInterval">M15</span>
        </div>
        <span class="text-[10px] text-[var(--muted)]">Powered by TradingView</span>
      </div>
      <div class="tradingview-widget-container flex-1 w-full min-h-0 relative">
        <div id="tradingview_chart" class="absolute inset-0 w-full h-full"></div>
      </div>
    </section>

    <!-- ---------- SIDE PANELS ---------- -->
    <aside class="terminal-side space-y-4 min-w-0">

      <!-- Primary trade plan -->
      <div class="side-panel plan-panel rounded-xl border border-[var(--brand)]/30 bg-[var(--surface)] overflow-hidden">
        <div class="panel-head flex items-center justify-between gap-2 px-4 h-11 border-b border-[var(--line)] bg-[var(--brand)]/8">
          <strong class="text-[12px] font-semibold text-[var(--brand)]">Primary trade plan</strong>
          <span class="text-[10px] text-[var(--muted)]">One clear decision</span>
        </div>
        <div id="tradePlan" class="p-4">
            <div class="terminal-empty">Loading plan…</div>
        </div>
      </div>

      <!-- Market analysis -->
      <div class="side-panel rounded-xl border border-[var(--line)] bg-[var(--surface)] overflow-hidden">
        <div class="panel-head flex items-center justify-between gap-2 px-4 h-11 border-b border-[var(--line)]">
          <strong class="text-[12px] font-semibold">Market analysis</strong>
          <span id="analysisMeta" class="text-[10px] text-[var(--muted)]"></span>
        </div>
        <div id="analysisList" class="divide-y divide-[var(--line)] max-h-[300px] overflow-y-auto thin-scroll">
            <div class="terminal-empty">Loading analysis…</div>
        </div>
        <p id="analysisVerdict" class="analysis-verdict px-4 py-3 text-[12px] leading-relaxed text-[var(--muted)] border-t border-[var(--line)] bg-[var(--raised)]/40"></p>
      </div>

      <!-- EA bot entries -->
      <div class="side-panel rounded-xl border border-[var(--line)] bg-[var(--surface)] overflow-hidden">
        <div class="panel-head flex items-center justify-between gap-2 px-4 h-11 border-b border-[var(--line)]">
          <strong class="text-[12px] font-semibold">EA bot entries</strong>
          <span id="botCount" class="num text-[10px] px-1.5 py-0.5 rounded bg-[var(--raised)] border border-[var(--line)] text-[var(--muted)]"></span>
        </div>
        <div id="botTrades" class="entry-list divide-y divide-[var(--line)] max-h-[300px] overflow-y-auto thin-scroll">
            <div class="terminal-empty">Loading bot entries…</div>
        </div>
      </div>

      <!-- Supporting entries -->
      <div class="side-panel rounded-xl border border-[var(--line)] bg-[var(--surface)] overflow-hidden">
        <div class="panel-head flex items-center justify-between gap-2 px-4 h-11 border-b border-[var(--line)]">
          <strong class="text-[12px] font-semibold">Supporting entries</strong>
          <a href="{{ route('signals.index') }}" class="text-[11px] font-medium text-[var(--brand)] hover:underline no-underline">All signals →</a>
        </div>
        <div id="entryList" class="entry-list divide-y divide-[var(--line)] max-h-[300px] overflow-y-auto thin-scroll">
            <div class="terminal-empty">Loading signals…</div>
        </div>
      </div>

      <!-- FVG profile -->
      <div class="side-panel rounded-xl border border-[var(--line)] bg-[var(--surface)] overflow-hidden">
        <div class="panel-head flex items-center justify-between gap-2 px-4 h-11 border-b border-[var(--line)]">
          <strong class="text-[12px] font-semibold">FVG profile</strong>
          <span id="fvgCount" class="num text-[10px] px-1.5 py-0.5 rounded bg-[var(--raised)] border border-[var(--line)] text-[var(--muted)]"></span>
        </div>
        <div id="fvgList" class="fvg-list divide-y divide-[var(--line)] max-h-[300px] overflow-y-auto thin-scroll">
            <div class="terminal-empty">Loading FVG profile…</div>
        </div>
      </div>

      <!-- Volume profile -->
      <div class="side-panel rounded-xl border border-[var(--line)] bg-[var(--surface)] overflow-hidden">
        <div class="panel-head flex items-center justify-between gap-2 px-4 h-11 border-b border-[var(--line)]">
          <strong class="text-[12px] font-semibold">Volume profile</strong>
          <span class="text-[10px] text-[var(--muted)]">POC areas</span>
        </div>
        <div id="volumeProfile" class="profile-list p-4 space-y-2.5 max-h-[300px] overflow-y-auto thin-scroll">
            <div class="terminal-empty">Loading volume profile…</div>
        </div>
      </div>
    </aside>
  </div>

  <div id="terminalWarning" class="feed-warning rounded-xl border border-[var(--warn)]/25 bg-[var(--warn)]/8 px-4 py-3 text-[12px] text-[var(--warn)]" hidden></div>
</div>
@endsection

@push('scripts')
<script type="text/javascript" src="https://s3.tradingview.com/tv.js"></script>
<script type="text/javascript">
document.addEventListener("DOMContentLoaded", function() {
    var currentTheme = document.documentElement.classList.contains('light') ? 'light' : 'dark';
    new TradingView.widget({
      "autosize": true,
      "symbol": "{{ $market->symbol == 'XAUUSD' ? 'OANDA:XAUUSD' : 'FX:'.$market->symbol }}",
      "interval": "15",
      "timezone": "Etc/UTC",
      "theme": currentTheme,
      "style": "1",
      "locale": "en",
      "enable_publishing": false,
      "backgroundColor": "rgba(0, 0, 0, 0)",
      "gridColor": currentTheme === 'light' ? "rgba(0, 0, 0, 0.06)" : "rgba(255, 255, 255, 0.04)",
      "hide_top_toolbar": false,
      "hide_legend": false,
      "save_image": false,
      "container_id": "tradingview_chart"
    });
});
</script>
<script src="{{ asset('js/terminal.js') }}" defer></script>
@endpush
