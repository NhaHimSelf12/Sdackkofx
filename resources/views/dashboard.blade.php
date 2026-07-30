@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="row">
  <div class="col-12 mb-4">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
      <div>
        <span class="badge bg-label-success me-2"><i class="bx bx-check-circle me-1"></i>System Active</span>
        <small class="text-muted">Last scan {{ optional($markets->max('analyzed_at'))?->diffForHumans() }}</small>
      </div>
      <form method="POST" action="{{ route('signals.refresh') }}">
        @csrf
        <button class="btn btn-primary" type="submit">
          <i class="bx bx-refresh me-1"></i> Refresh market & signals
        </button>
      </form>
    </div>
  </div>
</div>

@if(session('success'))
<div class="alert alert-success alert-dismissible" role="alert">
  {{ session('success') }}
  <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

@if(session('warning'))
<div class="alert alert-warning alert-dismissible" role="alert">
  {{ session('warning') }}
  <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

@if($markets->contains(fn($m) => ($m->data_status ?? 'demo') === 'demo'))
<div class="alert alert-danger alert-dismissible" role="alert">
  <h6 class="alert-heading d-flex align-items-center fw-bold mb-1">Demo Pricing Active</h6>
  <p class="mb-0">Some markets are using DEMO prices. Run <code>php artisan forex:feed-check --fresh</code>. Do not trade from demo values.</p>
  <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

<div class="row">
  <div class="col-lg-3 col-md-6 col-sm-6 mb-4">
    <div class="card">
      <div class="card-body">
        <div class="d-flex justify-content-between">
          <div class="card-info">
            <p class="card-text text-muted mb-1">Markets tracked</p>
            <div class="d-flex align-items-end mb-2">
              <h4 class="card-title mb-0 me-2">{{ $stats['markets'] }}</h4>
            </div>
            <small>{{ $stats['bullish_markets'] }} bullish &middot; {{ $stats['bearish_markets'] }} bearish</small>
          </div>
          <div class="card-icon">
            <span class="badge bg-label-primary rounded p-2">
              <i class="bx bx-store-alt bx-sm"></i>
            </span>
          </div>
        </div>
      </div>
    </div>
  </div>
  
  <div class="col-lg-3 col-md-6 col-sm-6 mb-4">
    <div class="card">
      <div class="card-body">
        <div class="d-flex justify-content-between">
          <div class="card-info">
            <p class="card-text text-muted mb-1">Active signals</p>
            <div class="d-flex align-items-end mb-2">
              <h4 class="card-title mb-0 me-2">{{ $stats['active_signals'] }}</h4>
            </div>
            <small>across all strategies</small>
          </div>
          <div class="card-icon">
            <span class="badge bg-label-info rounded p-2">
              <i class="bx bx-radar bx-sm"></i>
            </span>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="col-lg-3 col-md-6 col-sm-6 mb-4">
    <div class="card">
      <div class="card-body">
        <div class="d-flex justify-content-between">
          <div class="card-info">
            <p class="card-text text-muted mb-1">Buy entries</p>
            <div class="d-flex align-items-end mb-2">
              <h4 class="card-title text-success mb-0 me-2">{{ $stats['buy_signals'] }}</h4>
            </div>
            <small>long opportunities</small>
          </div>
          <div class="card-icon">
            <span class="badge bg-label-success rounded p-2">
              <i class="bx bx-trending-up bx-sm"></i>
            </span>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="col-lg-3 col-md-6 col-sm-6 mb-4">
    <div class="card">
      <div class="card-body">
        <div class="d-flex justify-content-between">
          <div class="card-info">
            <p class="card-text text-muted mb-1">Sell entries</p>
            <div class="d-flex align-items-end mb-2">
              <h4 class="card-title text-danger mb-0 me-2">{{ $stats['sell_signals'] }}</h4>
            </div>
            <small>short opportunities</small>
          </div>
          <div class="card-icon">
            <span class="badge bg-label-danger rounded p-2">
              <i class="bx bx-trending-down bx-sm"></i>
            </span>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<h5 class="pb-1 mb-4 mt-2">Markets</h5>
<div class="row mb-5">
  @foreach ($markets as $market)
  <div class="col-md-6 col-lg-4 col-xl-3 mb-4">
    <a href="{{ route('markets.show', $market) }}" class="card h-100 text-decoration-none">
      <div class="card-body">
        <div class="d-flex justify-content-between align-items-start">
          <div>
            <h5 class="card-title mb-1">{{ $market->symbol }}</h5>
            <small class="text-muted">{{ $market->name }}</small>
          </div>
          <span class="feed-chip feed-{{ $market->data_status ?? 'demo' }}">{{ strtoupper($market->data_status ?? 'demo') }}</span>
        </div>
        <div class="mt-3">
          <h4 class="mb-1">{{ number_format($market->price, $market->precision()) }}</h4>
          <div class="d-flex align-items-center">
            <span class="text-{{ $market->change_pct >= 0 ? 'success' : 'danger' }} fw-semibold">
              <i class="bx bx-{{ $market->change_pct >= 0 ? 'up' : 'down' }}-arrow-alt"></i>
              {{ number_format(abs($market->change_pct), 2) }}%
            </span>
            <span class="mx-2 text-muted">&middot;</span>
            <span class="badge bg-label-{{ $market->ai_bias === 'bullish' ? 'success' : ($market->ai_bias === 'bearish' ? 'danger' : 'secondary') }}">
              {{ ucfirst($market->ai_bias ?? 'neutral') }}
            </span>
          </div>
        </div>
      </div>
    </a>
  </div>
  @endforeach
</div>

<div class="row">
  <div class="col-xl-8 col-lg-7 col-12 mb-4">
    <div class="card mb-4">
      <h5 class="card-header">AI Market Analysis <small class="text-muted float-end">Highest Conviction</small></h5>
      <div class="card-body">
        @foreach ($markets->sortByDesc('ai_confidence')->take(3) as $market)
        <div class="d-flex mb-4 pb-1">
          <div class="avatar flex-shrink-0 me-3">
            <span class="avatar-initial rounded bg-label-{{ $market->ai_bias === 'bullish' ? 'success' : ($market->ai_bias === 'bearish' ? 'danger' : 'secondary') }}">
              <i class="bx bx-line-chart"></i>
            </span>
          </div>
          <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
            <div class="me-2">
              <a href="{{ route('markets.show', $market) }}" class="mb-0 fw-semibold text-body">{{ $market->symbol }}</a>
              <small class="text-muted d-block mt-1">{{ \Illuminate\Support\Str::limit($market->ai_summary, 100) }}</small>
            </div>
            <div class="user-progress d-flex align-items-center gap-1">
              <span class="fw-semibold">{{ $market->ai_confidence }}%</span>
              <div class="progress w-px-50" style="height: 6px;">
                <div class="progress-bar bg-primary" role="progressbar" style="width: {{ $market->ai_confidence }}%" aria-valuenow="{{ $market->ai_confidence }}" aria-valuemin="0" aria-valuemax="100"></div>
              </div>
            </div>
          </div>
        </div>
        @endforeach
      </div>
    </div>

    <div class="card">
      <h5 class="card-header">Top Entry Signals</h5>
      <div class="table-responsive text-nowrap">
        <table class="table table-hover">
          <thead>
            <tr>
              <th>Market</th>
              <th>Strategy</th>
              <th>Side</th>
              <th>Entry</th>
              <th>SL</th>
              <th>TP 3</th>
              <th>R:R</th>
              <th>Feed</th>
            </tr>
          </thead>
          <tbody class="table-border-bottom-0">
            @forelse ($signals as $signal)
            <tr>
              <td><a href="{{ route('markets.show', $signal->market) }}" class="fw-semibold">{{ $signal->market->symbol }}</a></td>
              <td>
                <span class="badge bg-label-primary">{{ $signal->strategy }}</span>
                @if($signal->is_primary) <i class="bx bxs-star text-warning"></i> @endif
              </td>
              <td><span class="badge bg-label-{{ $signal->direction === 'buy' ? 'success' : 'danger' }}">{{ strtoupper($signal->direction) }}</span></td>
              <td>{{ number_format($signal->entry, $signal->market->precision()) }}</td>
              <td class="text-danger">{{ number_format($signal->stop_loss, $signal->market->precision()) }}</td>
              <td class="text-success fw-bold">{{ number_format($signal->take_profit, $signal->market->precision()) }}</td>
              <td>{{ number_format($signal->risk_reward, 1) }}</td>
              <td><span class="feed-chip feed-{{ $signal->data_status }}">{{ strtoupper($signal->data_status ?? 'unknown') }}{{ ($signal->data_source ?? '') === 'yahoo' ? '' : ' · ' . strtoupper($signal->data_source ?? 'unknown') }}</span></td>
            </tr>
            @empty
            <tr><td colspan="8" class="text-center text-muted">No active signals — run <code>php artisan forex:scan</code>.</td></tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <div class="col-xl-4 col-lg-5 col-12 mb-4">
    <div class="card h-100">
      <h5 class="card-header">News & Sentiment</h5>
      <div class="card-body">
        <ul class="p-0 m-0">
          @forelse ($news as $item)
          <li class="d-flex mb-4 pb-1">
            <div class="avatar flex-shrink-0 me-3">
              <span class="avatar-initial rounded bg-label-{{ $item->sentiment === 'bullish' ? 'success' : ($item->sentiment === 'bearish' ? 'danger' : 'secondary') }}">
                <i class="bx bx-news"></i>
              </span>
            </div>
            <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
              <div class="me-2">
                <h6 class="mb-1 text-wrap">{{ $item->title }}</h6>
                <small class="text-muted d-block">
                  {{ $item->source }} &middot; {{ optional($item->published_at)?->diffForHumans() }}
                </small>
              </div>
              @if ($item->impact === 'high')
              <div class="user-progress">
                <span class="badge bg-label-warning">High Impact</span>
              </div>
              @endif
            </div>
          </li>
          @empty
          <li class="text-center text-muted">No news yet.</li>
          @endforelse
        </ul>
      </div>
    </div>
  </div>
</div>
@endsection
