@extends('layouts.app')

@section('title', 'Signals')

@section('content')
<h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Signals /</span> Entry signals from all strategies</h4>

<div class="row mb-4">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div>
                <span class="badge bg-label-success me-2"><i class="bx bx-check-circle me-1"></i>Only verified remote feeds generate signals</span>
            </div>
            <form method="POST" action="{{ route('signals.refresh') }}">
                @csrf
                <button class="btn btn-primary" type="submit">
                    <i class="bx bx-refresh me-1"></i> Refresh signals
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

<div class="row mb-4">
    <div class="col-12">
        <div class="d-flex flex-wrap gap-2">
            <a class="btn rounded-pill btn-{{ !request('direction') && !request('strategy') && !request('status') ? 'primary' : 'outline-primary' }}" href="{{ route('signals.index') }}">All</a>
            <a class="btn rounded-pill btn-{{ request('direction') === 'buy' ? 'success' : 'outline-success' }}" href="{{ route('signals.index', ['direction' => 'buy']) }}">Buy</a>
            <a class="btn rounded-pill btn-{{ request('direction') === 'sell' ? 'danger' : 'outline-danger' }}" href="{{ route('signals.index', ['direction' => 'sell']) }}">Sell</a>
            
            <a class="btn rounded-pill btn-{{ request('strategy') === 'SMC' ? 'secondary' : 'outline-secondary' }}" href="{{ route('signals.index', ['strategy' => 'SMC']) }}">SMC</a>
            <a class="btn rounded-pill btn-{{ request('strategy') === 'ICT' ? 'secondary' : 'outline-secondary' }}" href="{{ route('signals.index', ['strategy' => 'ICT']) }}">ICT</a>
            <a class="btn rounded-pill btn-{{ request('strategy') === 'MSNR' ? 'secondary' : 'outline-secondary' }}" href="{{ route('signals.index', ['strategy' => 'MSNR']) }}">MSNR</a>
            <a class="btn rounded-pill btn-{{ request('strategy') === 'TECH' ? 'secondary' : 'outline-secondary' }}" href="{{ route('signals.index', ['strategy' => 'TECH']) }}">TECH</a>
            
            <a class="btn rounded-pill btn-{{ request('status') === 'expired' ? 'warning' : 'outline-warning' }}" href="{{ route('signals.index', ['status' => 'expired']) }}">Expired</a>
        </div>
    </div>
</div>

<div class="card">
    <div class="table-responsive text-nowrap">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>Market</th>
                    <th>TF</th>
                    <th>Strategy</th>
                    <th>Side</th>
                    <th>Entry</th>
                    <th>Stop loss</th>
                    <th>Take profit</th>
                    <th>R:R</th>
                    <th>Feed</th>
                    <th>Expires</th>
                    <th>Confidence</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody class="table-border-bottom-0">
                @forelse ($signals as $signal)
                <tr>
                    <td>
                        <a href="{{ route('markets.show', $signal->market) }}" class="fw-semibold">
                            {{ $signal->market->symbol }}
                        </a>
                    </td>
                    <td class="text-muted">{{ $signal->timeframe }}</td>
                    <td>
                        <span class="badge bg-label-primary">{{ $signal->strategy }}</span>
                        @if($signal->is_primary) 
                        <i class="bx bxs-star text-warning" title="Primary trade plan for this market"></i>
                        @endif
                    </td>
                    <td><span class="badge bg-label-{{ $signal->direction === 'buy' ? 'success' : 'danger' }}">{{ strtoupper($signal->direction) }}</span></td>
                    <td>{{ number_format($signal->entry, $signal->market->precision()) }}</td>
                    <td class="text-danger">{{ number_format($signal->stop_loss, $signal->market->precision()) }}</td>
                    <td class="text-success">
                        <small class="text-muted d-block">{{ number_format($signal->tp1, $signal->market->precision()) }}</small>
                        <small class="text-muted d-block">{{ number_format($signal->tp2, $signal->market->precision()) }}</small>
                        <strong class="d-block">{{ number_format($signal->take_profit, $signal->market->precision()) }}</strong>
                    </td>
                    <td>{{ number_format($signal->risk_reward, 1) }}</td>
                    <td><span class="feed-chip feed-{{ $signal->data_status }}">{{ strtoupper($signal->data_status ?? 'unknown') }}{{ ($signal->data_source ?? '') === 'yahoo' ? '' : ' · ' . strtoupper($signal->data_source ?? 'unknown') }}</span></td>
                    <td class="text-muted">{{ optional($signal->expires_at)?->diffForHumans() ?? '—' }}</td>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <div class="progress w-px-50" style="height: 6px;">
                                <div class="progress-bar bg-primary" role="progressbar" style="width: {{ $signal->confidence }}%" aria-valuenow="{{ $signal->confidence }}" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                            <small>{{ $signal->confidence }}%</small>
                        </div>
                    </td>
                    <td>
                        <button class="btn btn-sm btn-outline-primary" onclick="navigator.clipboard.writeText('{{ $signal->direction === 'buy' ? 'BUY' : 'SELL' }} {{ $signal->market->symbol }} \nEntry: {{ number_format($signal->entry, $signal->market->precision()) }} \nSL: {{ number_format($signal->stop_loss, $signal->market->precision()) }} \nTP1: {{ number_format($signal->tp1, $signal->market->precision()) }} \nTP2: {{ number_format($signal->tp2, $signal->market->precision()) }} \nTP3: {{ number_format($signal->take_profit, $signal->market->precision()) }}').then(()=>alert('Copied to clipboard!'))">
                            <i class="bx bx-copy"></i> Copy
                        </button>
                    </td>
                </tr>
                @if($signal->note)
                <tr>
                    <td colspan="12" class="text-muted border-bottom-0 py-2"><i class="bx bx-info-circle me-1"></i> {{ $signal->note }}</td>
                </tr>
                @endif
                @empty
                <tr><td colspan="12" class="text-center text-muted py-4">No signals found — run <code>php artisan forex:scan</code>.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="d-flex justify-content-center mt-4">
    <nav aria-label="Page navigation">
        <ul class="pagination">
            <li class="page-item {{ $signals->onFirstPage() ? 'disabled' : '' }}">
                <a class="page-link" href="{{ $signals->previousPageUrl() ?? '#' }}"><i class="tf-icon bx bx-chevron-left"></i> Previous</a>
            </li>
            <li class="page-item disabled">
                <span class="page-link">Page {{ $signals->currentPage() }} of {{ $signals->lastPage() }}</span>
            </li>
            <li class="page-item {{ !$signals->hasMorePages() ? 'disabled' : '' }}">
                <a class="page-link" href="{{ $signals->nextPageUrl() ?? '#' }}">Next <i class="tf-icon bx bx-chevron-right"></i></a>
            </li>
        </ul>
    </nav>
</div>
@endsection
