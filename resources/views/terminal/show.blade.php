@extends('layouts.app')
@section('title','Trading Terminal')

@push('styles')
<style>
  .terminal-select { background: transparent; border: 1px solid #d9dee3; padding: 4px 12px; border-radius: 4px; color: #697a8d; cursor: pointer; }
  .tf-button { background: transparent; border: 1px solid #d9dee3; padding: 4px 12px; border-radius: 4px; color: #697a8d; cursor: pointer; transition: all 0.2s; }
  .tf-button.active { background: #696cff; color: #fff; border-color: #696cff; }
  .indicator-bar label { font-size: 13px; margin-right: 15px; display: inline-flex; align-items: center; cursor: pointer; color: #566a7f; }
  .indicator-bar input[type="checkbox"] { margin-right: 6px; }
  .terminal-status { font-size: 12px; color: #a1acb8; }
  
  .side-panel { margin-bottom: 1rem; }
  .side-panel .card-header { padding: 1rem; display: flex; justify-content: space-between; align-items: center; }
  .side-panel .card-body { padding: 1rem; }
  .side-panel strong { display: block; font-size: 14px; margin-bottom: 2px; }
  .side-panel span, .side-panel a { font-size: 12px; color: #a1acb8; text-decoration: none; }
  
  .terminal-empty { font-size: 13px; color: #a1acb8; text-align: center; padding: 1rem 0; }
  .entry-list, .fvg-list, .profile-list { font-size: 13px; max-height: 250px; overflow-y: auto; }
</style>
@endpush

@section('content')
<div id="terminal" data-endpoint="{{ route('terminal.data',$market) }}" data-symbol="{{ $market->symbol }}">
    
  <div class="card mb-4">
    <div class="card-body d-flex flex-wrap justify-content-between align-items-center gap-3">
        <div class="d-flex align-items-center gap-3">
            <select id="marketSelect" class="form-select w-auto" aria-label="Market">
                @foreach($markets as $m)
                <option value="{{ route('terminal.show',$m) }}" @selected($m->id===$market->id)>{{ $m->symbol }} &middot; {{ $m->name }}</option>
                @endforeach
            </select>
            <div>
                <h4 class="mb-0 d-inline-block me-2" id="livePrice">{{ number_format($market->price,$market->precision()) }}</h4>
                <span id="liveChange" class="fw-semibold text-{{ $market->change_pct>=0?'success':'danger' }}">
                    <i class="bx bx-{{ $market->change_pct>=0?'up':'down' }}-arrow-alt"></i>
                    {{ number_format(abs($market->change_pct),2) }}%
                </span>
            </div>
        </div>
        
        <div class="d-flex align-items-center gap-3">
            <div class="tf-group d-flex gap-1" role="group" aria-label="Timeframe">
                @foreach(['M1','M5','M15','H1','H4','D1'] as $tf)
                <button class="tf-button {{ $tf==='M1'?'active':'' }}" data-timeframe="{{ $tf }}">{{ $tf }}</button>
                @endforeach
            </div>
            
            <div class="d-none d-md-flex flex-column align-items-end mx-3 text-muted" style="font-size:12px;">
                <span>Next candle</span>
                <strong id="candleCountdown">--:--</strong>
            </div>
            
            <div id="feedBadge" class="feed-chip feed-{{ $market->data_status }}">{{ strtoupper($market->data_status) }}{{ $market->data_source === 'yahoo' ? '' : ' · ' . strtoupper($market->data_source) }}</div>
        </div>
    </div>
  </div>

  <div class="card mb-4">
      <div class="card-body py-3 d-flex flex-wrap align-items-center indicator-bar">
          <span class="fw-semibold me-3"><i class="bx bx-slider me-1"></i> Indicators:</span>
          <label><input type="checkbox" data-layer="fvg" checked class="form-check-input"> FVG zones</label>
          <label><input type="checkbox" data-layer="volume" checked class="form-check-input"> Volume</label>
          <label><input type="checkbox" data-layer="profile" checked class="form-check-input"> Volume profile</label>
          <label><input type="checkbox" data-layer="trendlines" checked class="form-check-input"> Trendlines</label>
          <label><input type="checkbox" data-layer="signals" checked class="form-check-input"> Buy/Sell entries</label>
          <label><input type="checkbox" data-layer="bots" checked class="form-check-input"> Bot entries</label>
          <span id="terminalStatus" class="terminal-status ms-auto"><i class="bx bx-loader-alt bx-spin"></i> Connecting…</span>
      </div>
  </div>

  <div class="row">
    <div class="col-xl-9 col-lg-8 mb-4">
      <div class="card h-100" style="min-height: 600px; display: flex; flex-direction: column;">
          <div class="card-body p-0" style="flex: 1; display: flex;">
            <!-- TradingView Widget BEGIN -->
            <div class="tradingview-widget-container" style="flex: 1; width: 100%;">
                <div id="tradingview_chart" style="height: 100%; width: 100%;"></div>
                <script type="text/javascript" src="https://s3.tradingview.com/tv.js"></script>
                <script type="text/javascript">
                var currentTheme = document.documentElement.classList.contains('dark-style') ? 'dark' : 'light';
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
                "gridColor": currentTheme === 'dark' ? "rgba(255, 255, 255, 0.05)" : "rgba(0, 0, 0, 0.05)",
                "hide_top_toolbar": false,
                "hide_legend": false,
                "save_image": false,
                "container_id": "tradingview_chart"
                });
                </script>
            </div>
            <!-- TradingView Widget END -->
          </div>
      </div>
    </div>
    
    <div class="col-xl-3 col-lg-4">
      <div class="card side-panel plan-panel border-primary mb-4">
          <div class="card-header border-bottom bg-label-primary">
              <div>
                  <strong class="text-primary mb-0"><i class="bx bx-target-lock me-1"></i>Primary trade plan</strong>
                  <span class="text-primary">One clear decision</span>
              </div>
          </div>
          <div class="card-body" id="tradePlan">
              <div class="terminal-empty">Loading plan…</div>
          </div>
      </div>
      
      <div class="card side-panel mb-4">
          <div class="card-header border-bottom">
              <div>
                  <strong class="mb-0">Market analysis</strong>
                  <span id="analysisMeta"></span>
              </div>
          </div>
          <div class="card-body pb-2">
              <div id="analysisList"></div>
              <p id="analysisVerdict" class="fw-semibold mt-2 mb-0"></p>
          </div>
      </div>
      
      <div class="card side-panel mb-4">
          <div class="card-header border-bottom">
              <div>
                  <strong class="mb-0">EA bot entries</strong>
                  <span id="botCount"></span>
              </div>
          </div>
          <div class="card-body p-0">
              <div id="botTrades" class="entry-list p-3"><div class="terminal-empty">Loading bot entries…</div></div>
          </div>
      </div>
      
      <div class="card side-panel mb-4">
          <div class="card-header border-bottom">
              <div>
                  <strong class="mb-0">Supporting entries</strong>
                  <a href="{{ route('signals.index') }}" class="text-primary">All signals</a>
              </div>
          </div>
          <div class="card-body p-0">
              <div id="entryList" class="entry-list p-3"><div class="terminal-empty">Loading signals…</div></div>
          </div>
      </div>
      
      <div class="card side-panel mb-4">
          <div class="card-header border-bottom">
              <div>
                  <strong class="mb-0">FVG profile</strong>
                  <span id="fvgCount">0 zones</span>
              </div>
          </div>
          <div class="card-body p-0">
              <div id="fvgList" class="fvg-list p-3"></div>
          </div>
      </div>
      
      <div class="card side-panel">
          <div class="card-header border-bottom">
              <div>
                  <strong class="mb-0">Volume profile</strong>
                  <span>POC areas</span>
              </div>
          </div>
          <div class="card-body p-0">
              <div id="volumeProfile" class="profile-list p-3"></div>
          </div>
      </div>
    </div>
  </div>
  
  <div id="terminalWarning" class="alert alert-danger" hidden></div>
</div>
@endsection
@push('scripts')<script src="{{ asset('js/terminal.js') }}" defer></script>@endpush
