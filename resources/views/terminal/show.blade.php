@extends('layouts.app')
@section('title','Trading Terminal')
@section('subtitle','Live candles, FVG, volume profile, trendlines and entries')
@section('content')
<div id="terminal" class="terminal" data-endpoint="{{ route('terminal.data',$market) }}" data-symbol="{{ $market->symbol }}">
  <div class="terminal-top card">
    <div class="terminal-market">
      <select id="marketSelect" class="terminal-select" aria-label="Market">
        @foreach($markets as $m)<option value="{{ route('terminal.show',$m) }}" @selected($m->id===$market->id)>{{ $m->symbol }} · {{ $m->name }}</option>@endforeach
      </select>
      <div><strong id="livePrice">{{ number_format($market->price,$market->precision()) }}</strong><span id="liveChange" class="{{ $market->change_pct>=0?'up':'down' }}">{{ $market->change_pct>=0?'+':'' }}{{ number_format($market->change_pct,2) }}%</span></div>
    </div>
    <div class="tf-group" role="group" aria-label="Timeframe">
      @foreach(['M1','M5','M15','H1','H4','D1'] as $tf)<button class="tf-button {{ $tf==='M1'?'active':'' }}" data-timeframe="{{ $tf }}">{{ $tf }}</button>@endforeach
    </div>
    <div class="candle-clock"><span>Next candle</span><strong id="candleCountdown">--:--</strong></div>
    <div id="feedBadge" class="feed-chip feed-{{ $market->data_status }}">{{ strtoupper($market->data_status.' · '.$market->data_source) }}</div>
  </div>

  <div class="indicator-bar card">
    <span>Indicators</span>
    <label><input type="checkbox" data-layer="fvg" checked> FVG zones</label>
    <label><input type="checkbox" data-layer="volume" checked> Volume</label>
    <label><input type="checkbox" data-layer="profile" checked> Volume profile</label>
    <label><input type="checkbox" data-layer="trendlines" checked> Trendlines</label>
    <label><input type="checkbox" data-layer="signals" checked> Buy/Sell entries</label>
    <label><input type="checkbox" data-layer="bots" checked> Bot entries</label>
    <span id="terminalStatus" class="terminal-status">Connecting…</span>
  </div>

  <div class="terminal-grid">
    <section class="terminal-chart card" style="padding: 0; overflow: hidden; min-height: 500px; display: flex; flex-direction: column;">
      <!-- TradingView Widget BEGIN -->
      <div class="tradingview-widget-container" style="flex: 1; width: 100%;">
        <div id="tradingview_chart" style="height: 100%; width: 100%;"></div>
        <script type="text/javascript" src="https://s3.tradingview.com/tv.js"></script>
        <script type="text/javascript">
        var currentTheme = document.documentElement.getAttribute('data-theme') === 'light' ? 'light' : 'dark';
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
        </script>
      </div>
      <!-- TradingView Widget END -->
    </section>
    <aside class="terminal-side">
      <div class="card side-panel plan-panel"><div class="panel-head"><strong>Primary trade plan</strong><span>One clear decision</span></div><div id="tradePlan"><div class="terminal-empty">Loading plan…</div></div></div>
      <div class="card side-panel"><div class="panel-head"><strong>Market analysis</strong><span id="analysisMeta"></span></div><div id="analysisList"></div><p id="analysisVerdict" class="analysis-verdict"></p></div>
      <div class="card side-panel"><div class="panel-head"><strong>EA bot entries</strong><span id="botCount"></span></div><div id="botTrades" class="entry-list"><div class="terminal-empty">Loading bot entries…</div></div></div>
      <div class="card side-panel"><div class="panel-head"><strong>Supporting entries</strong><a href="{{ route('signals.index') }}">All signals</a></div><div id="entryList" class="entry-list"><div class="terminal-empty">Loading signals…</div></div></div>
      <div class="card side-panel"><div class="panel-head"><strong>FVG profile</strong><span id="fvgCount">0 zones</span></div><div id="fvgList" class="fvg-list"></div></div>
      <div class="card side-panel"><div class="panel-head"><strong>Volume profile</strong><span>POC areas</span></div><div id="volumeProfile" class="profile-list"></div></div>
    </aside>
  </div>
  <div id="terminalWarning" class="feed-warning" hidden></div>
</div>
@endsection
@push('scripts')<script src="{{ asset('js/terminal.js') }}" defer></script>@endpush
