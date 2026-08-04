@extends('layouts.app')
@section('title','Plan a Trade')
@section('subtitle','Define the setup and risk before entering')
@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2 rounded-xl border border-[var(--line)] bg-[var(--surface)] p-6">
        <form method="POST" action="{{ route('journal.store') }}" class="grid grid-cols-1 sm:grid-cols-2 gap-5">
            @csrf
            <div class="flex flex-col gap-2">
                <label class="text-[12px] font-semibold text-[var(--muted)] uppercase tracking-wider">Market</label>
                <select name="market_id" required class="bg-[var(--base)] border border-[var(--line)] text-[var(--text)] text-[13px] font-semibold px-4 py-2.5 rounded-lg outline-none cursor-pointer hover:border-[var(--brand)]/50 transition-colors w-full focus:border-[var(--brand)] focus:ring-1 focus:ring-[var(--brand)]">
                    @foreach($markets as $market)
                    <option value="{{ $market->id }}">{{ $market->symbol }} — {{ $market->name }}</option>
                    @endforeach
                </select>
            </div>
            
            <div class="flex flex-col gap-2">
                <label class="text-[12px] font-semibold text-[var(--muted)] uppercase tracking-wider">Direction</label>
                <select name="direction" class="bg-[var(--base)] border border-[var(--line)] text-[var(--text)] text-[13px] font-semibold px-4 py-2.5 rounded-lg outline-none cursor-pointer hover:border-[var(--brand)]/50 transition-colors w-full focus:border-[var(--brand)] focus:ring-1 focus:ring-[var(--brand)]">
                    <option value="buy">Buy</option>
                    <option value="sell">Sell</option>
                </select>
            </div>

            <div class="flex flex-col gap-2">
                <label class="text-[12px] font-semibold text-[var(--muted)] uppercase tracking-wider">Strategy</label>
                <select name="strategy" class="bg-[var(--base)] border border-[var(--line)] text-[var(--text)] text-[13px] font-semibold px-4 py-2.5 rounded-lg outline-none cursor-pointer hover:border-[var(--brand)]/50 transition-colors w-full focus:border-[var(--brand)] focus:ring-1 focus:ring-[var(--brand)]">
                    <option>SMC</option>
                    <option>ICT</option>
                    <option>MSNR</option>
                    <option>Price Action</option>
                </select>
            </div>

            <div class="flex flex-col gap-2">
                <label class="text-[12px] font-semibold text-[var(--muted)] uppercase tracking-wider">Timeframe</label>
                <select name="timeframe" class="bg-[var(--base)] border border-[var(--line)] text-[var(--text)] text-[13px] font-semibold px-4 py-2.5 rounded-lg outline-none cursor-pointer hover:border-[var(--brand)]/50 transition-colors w-full focus:border-[var(--brand)] focus:ring-1 focus:ring-[var(--brand)]">
                    <option>M15</option>
                    <option selected>H1</option>
                    <option>H4</option>
                    <option>D1</option>
                </select>
            </div>

            <div class="flex flex-col gap-2">
                <label class="text-[12px] font-semibold text-[var(--muted)] uppercase tracking-wider">Entry</label>
                <input type="number" step="any" name="entry" required class="bg-[var(--base)] border border-[var(--line)] text-[var(--text)] text-[13px] px-4 py-2.5 rounded-lg outline-none w-full hover:border-[var(--brand)]/50 focus:border-[var(--brand)] focus:ring-1 focus:ring-[var(--brand)] transition-colors num">
            </div>

            <div class="flex flex-col gap-2">
                <label class="text-[12px] font-semibold text-[var(--muted)] uppercase tracking-wider">Stop loss</label>
                <input type="number" step="any" name="stop_loss" required class="bg-[var(--base)] border border-[var(--line)] text-[var(--text)] text-[13px] px-4 py-2.5 rounded-lg outline-none w-full hover:border-[var(--down)]/50 focus:border-[var(--down)] focus:ring-1 focus:ring-[var(--down)] transition-colors num">
            </div>

            <div class="flex flex-col gap-2">
                <label class="text-[12px] font-semibold text-[var(--muted)] uppercase tracking-wider">Take profit</label>
                <input type="number" step="any" name="take_profit" class="bg-[var(--base)] border border-[var(--line)] text-[var(--text)] text-[13px] px-4 py-2.5 rounded-lg outline-none w-full hover:border-[var(--up)]/50 focus:border-[var(--up)] focus:ring-1 focus:ring-[var(--up)] transition-colors num">
            </div>

            <div class="flex flex-col gap-2">
                <label class="text-[12px] font-semibold text-[var(--muted)] uppercase tracking-wider">Risk %</label>
                <input type="number" step="0.1" min="0.1" max="10" name="risk_pct" value="{{ auth()->user()->default_risk_pct }}" required class="bg-[var(--base)] border border-[var(--line)] text-[var(--text)] text-[13px] px-4 py-2.5 rounded-lg outline-none w-full hover:border-[var(--brand)]/50 focus:border-[var(--brand)] focus:ring-1 focus:ring-[var(--brand)] transition-colors num">
            </div>

            <div class="sm:col-span-2 flex flex-col gap-2">
                <label class="text-[12px] font-semibold text-[var(--muted)] uppercase tracking-wider">Setup notes</label>
                <textarea name="setup_notes" rows="5" placeholder="Why this setup? BOS, FVG, liquidity, session, confirmation..." class="bg-[var(--base)] border border-[var(--line)] text-[var(--text)] text-[13px] px-4 py-2.5 rounded-lg outline-none w-full hover:border-[var(--brand)]/50 focus:border-[var(--brand)] focus:ring-1 focus:ring-[var(--brand)] transition-colors resize-y min-h-[100px]"></textarea>
            </div>

            <div class="sm:col-span-2 flex items-center justify-end gap-3 pt-4 border-t border-[var(--line)]">
                <a href="{{ route('journal.index') }}" class="px-5 py-2.5 rounded-lg border border-[var(--line)] bg-[var(--surface)] text-[13px] font-semibold hover:bg-[var(--raised)] transition no-underline text-[var(--text)]">Cancel</a>
                <button type="submit" class="px-6 py-2.5 rounded-lg bg-[var(--brand)] text-white text-[13px] font-semibold hover:opacity-90 transition border-none cursor-pointer shadow-lg shadow-[var(--brand)]/20">Save trade plan</button>
            </div>
        </form>
    </div>

    <div class="rounded-xl border border-[var(--line)] bg-[var(--surface)] p-6 self-start">
        <div class="text-[11px] uppercase tracking-[0.14em] text-[var(--muted)] mb-4 font-semibold">Account rules</div>
        <h2 class="text-[28px] font-bold num text-[var(--text)] mb-4 pb-4 border-b border-[var(--line)]">${{ number_format(auth()->user()->account_balance, 2) }}</h2>
        <p class="text-[13px] text-[var(--muted)] leading-relaxed mb-6">Default risk: <strong class="text-[var(--text)]">{{ auth()->user()->default_risk_pct }}%</strong>. Position size is calculated automatically from entry and stop-loss.</p>
        
        <div class="bg-[var(--brand)]/10 border border-[var(--brand)]/20 rounded-lg p-4 text-[12px] text-[var(--brand)] leading-relaxed">
            <strong class="block mb-1">Best practice:</strong>
            Risk 0.5–1% per trade and never widen a stop-loss after entry.
        </div>
    </div>
</div>
@endsection
