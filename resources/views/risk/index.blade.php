@extends('layouts.app')
@section('title','Risk Calculator')
@section('subtitle','Position sizing before every trade')
@section('content')
<div class="form-layout"><div class="card"><form method="POST" action="{{ route('risk.calculate') }}" class="form-grid">@csrf
<label>Market<select class="input" name="market_id">@foreach($markets as $m)<option value="{{ $m->id }}" @selected(old('market_id',request('market_id'))==$m->id)>{{ $m->symbol }}</option>@endforeach</select></label>
<label>Account balance<input class="input" type="number" step="0.01" name="balance" value="{{ old('balance',auth()->user()->account_balance) }}" required></label>
<label>Risk %<input class="input" type="number" step="0.1" min="0.1" max="10" name="risk_pct" value="{{ old('risk_pct',auth()->user()->default_risk_pct) }}" required></label>
<label>Entry price<input class="input" type="number" step="any" name="entry" value="{{ old('entry') }}" required></label>
<label>Stop-loss price<input class="input" type="number" step="any" name="stop_loss" value="{{ old('stop_loss') }}" required></label>
<div class="full"><button class="btn btn-primary">Calculate position</button></div></form></div>
<div class="card form-aside">@if($result)<div class="stat-label">Recommended position</div><div class="risk-result">{{ $result['lot_size'] }} lots</div><div class="metric-list"><div><span>Risk amount</span><strong>${{ number_format($result['risk_amount'],2) }}</strong></div><div><span>Stop distance</span><strong>{{ $result['stop_distance'] }}</strong></div><div><span>Approx. pips/points</span><strong>{{ $result['stop_pips'] }}</strong></div></div><p class="muted small">Estimate only — verify contract size with your broker.</p>@else<div class="empty-state">Enter balance, risk and stop-loss to calculate the suggested lot size.</div>@endif</div></div>
@endsection
