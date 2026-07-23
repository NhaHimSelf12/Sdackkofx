@extends('layouts.app')
@section('title','Trading Journal')
@section('subtitle','Measure performance and trading discipline')
@section('content')
<div class="action-row"><div class="chip-row" style="margin:0"><span class="chip active">All trades</span></div><a class="btn btn-primary" href="{{ route('journal.create') }}">+ Plan a trade</a></div>
@if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
<div class="grid-stats journal-stats">
<div class="card"><div class="stat-label">Closed trades</div><div class="stat-value">{{ $stats['total'] }}</div></div>
<div class="card"><div class="stat-label">Win rate</div><div class="stat-value">{{ $stats['win_rate'] }}%</div></div>
<div class="card"><div class="stat-label">Net P/L</div><div class="stat-value {{ $stats['net_pnl'] >= 0 ? 'up':'down' }}">${{ number_format($stats['net_pnl'],2) }}</div></div>
<div class="card"><div class="stat-label">Average R</div><div class="stat-value">{{ number_format($stats['avg_r'],2) }}R</div></div>
</div>
<div class="section-title">Trade history</div><div class="card" style="padding:8px 4px"><div class="table-wrap"><table class="table"><thead><tr><th>Market</th><th>Side</th><th>Strategy</th><th>Entry</th><th>Risk</th><th>Lot</th><th>Status</th><th>P/L</th><th>Date</th></tr></thead><tbody>
@forelse($trades as $trade)<tr><td class="cell-strong">{{ $trade->market->symbol }}</td><td><span class="badge {{ $trade->direction==='buy'?'badge-buy':'badge-sell' }}">{{ strtoupper($trade->direction) }}</span></td><td>{{ $trade->strategy ?: '—' }}</td><td>{{ number_format($trade->entry,$trade->market->precision()) }}</td><td>${{ number_format($trade->risk_amount,2) }}</td><td>{{ $trade->lot_size }}</td><td><span class="badge badge-neutral">{{ ucfirst($trade->status) }}</span></td><td class="{{ ($trade->profit_loss??0)>=0?'up':'down' }}">{{ $trade->profit_loss===null?'—':'$'.number_format($trade->profit_loss,2) }}</td><td class="muted">{{ $trade->created_at->format('d M Y') }}</td></tr>
@empty<tr><td colspan="9" class="empty-state">No trades yet. Start by planning your first trade.</td></tr>@endforelse
</tbody></table></div></div>
@endsection
