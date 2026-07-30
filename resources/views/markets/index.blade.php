@extends('layouts.app')

@section('title', 'Markets')

@section('content')
<h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Markets /</span> All tracked instruments</h4>

<div class="row">
    @foreach ($markets as $category => $group)
    <div class="col-12 mb-4">
        <div class="card">
            <h5 class="card-header text-capitalize">{{ $category }}</h5>
            <div class="table-responsive text-nowrap">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Symbol</th>
                            <th>Name</th>
                            <th>Price</th>
                            <th>Change</th>
                            <th>AI Bias</th>
                            <th>Confidence</th>
                            <th>Signals</th>
                        </tr>
                    </thead>
                    <tbody class="table-border-bottom-0">
                        @foreach ($group as $market)
                        <tr>
                            <td>
                                <a href="{{ route('markets.show', $market) }}" class="fw-semibold">
                                    <i class="bx bx-trending-up text-primary me-2"></i>{{ $market->symbol }}
                                </a>
                            </td>
                            <td class="text-muted">{{ $market->name }}</td>
                            <td class="fw-medium">{{ number_format($market->price, $market->precision()) }}</td>
                            <td>
                                <span class="text-{{ $market->change_pct >= 0 ? 'success' : 'danger' }} fw-semibold">
                                    <i class="bx bx-{{ $market->change_pct >= 0 ? 'up' : 'down' }}-arrow-alt"></i>
                                    {{ number_format(abs($market->change_pct), 2) }}%
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-label-{{ $market->ai_bias === 'bullish' ? 'success' : ($market->ai_bias === 'bearish' ? 'danger' : 'secondary') }}">
                                    {{ ucfirst($market->ai_bias ?? 'neutral') }}
                                </span>
                            </td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="progress w-px-50" style="height: 6px;">
                                        <div class="progress-bar bg-primary" role="progressbar" style="width: {{ $market->ai_confidence }}%" aria-valuenow="{{ $market->ai_confidence }}" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                    <small>{{ $market->ai_confidence }}%</small>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-label-primary rounded-pill">{{ $market->active_signals_count }} active</span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endforeach
</div>
@endsection
