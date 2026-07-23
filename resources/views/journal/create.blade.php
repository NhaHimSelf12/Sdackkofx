@extends('layouts.app')
@section('title','Plan a Trade')
@section('subtitle','Define the setup and risk before entering')
@section('content')
<div class="form-layout"><div class="card"><form method="POST" action="{{ route('journal.store') }}" class="form-grid">@csrf
<label>Market<select class="input" name="market_id" required>@foreach($markets as $market)<option value="{{ $market->id }}">{{ $market->symbol }} — {{ $market->name }}</option>@endforeach</select></label>
<label>Direction<select class="input" name="direction"><option value="buy">Buy</option><option value="sell">Sell</option></select></label>
<label>Strategy<select class="input" name="strategy"><option>SMC</option><option>ICT</option><option>MSNR</option><option>Price Action</option></select></label>
<label>Timeframe<select class="input" name="timeframe"><option>M15</option><option selected>H1</option><option>H4</option><option>D1</option></select></label>
<label>Entry<input class="input" type="number" step="any" name="entry" required></label>
<label>Stop loss<input class="input" type="number" step="any" name="stop_loss" required></label>
<label>Take profit<input class="input" type="number" step="any" name="take_profit"></label>
<label>Risk %<input class="input" type="number" step="0.1" min="0.1" max="10" name="risk_pct" value="{{ auth()->user()->default_risk_pct }}" required></label>
<label class="full">Setup notes<textarea class="input" name="setup_notes" rows="5" placeholder="Why this setup? BOS, FVG, liquidity, session, confirmation..."></textarea></label>
<div class="full action-row"><a class="btn" href="{{ route('journal.index') }}">Cancel</a><button class="btn btn-primary">Save trade plan</button></div>
</form></div><div class="card form-aside"><div class="stat-label">Account rules</div><h2>${{ number_format(auth()->user()->account_balance,2) }}</h2><p class="muted">Default risk: {{ auth()->user()->default_risk_pct }}%. Position size is calculated automatically from entry and stop-loss.</p><div class="risk-note">Best practice: risk 0.5–1% per trade and never widen a stop-loss after entry.</div></div></div>
@endsection
