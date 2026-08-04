@extends('layouts.app')

@section('title', 'Risk Calculator')
@section('subtitle', 'Position sizing before every trade')

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2 rounded-xl border border-[var(--line)] bg-[var(--surface)] p-6">
        <form method="POST" action="{{ route('risk.calculate') }}" class="grid grid-cols-1 sm:grid-cols-2 gap-5">
            @csrf
            
            <div class="flex flex-col gap-2">
                <label class="text-[12px] font-semibold text-[var(--muted)] uppercase tracking-wider">Market</label>
                <select name="market_id" class="bg-[var(--base)] border border-[var(--line)] text-[var(--text)] text-[13px] font-semibold px-4 py-2.5 rounded-lg outline-none cursor-pointer hover:border-[var(--brand)]/50 transition-colors w-full focus:border-[var(--brand)] focus:ring-1 focus:ring-[var(--brand)]">
                    @foreach($markets as $m)
                        <option value="{{ $m->id }}" @selected(old('market_id', request('market_id')) == $m->id)>{{ $m->symbol }}</option>
                    @endforeach
                </select>
            </div>
            
            <div class="flex flex-col gap-2">
                <label class="text-[12px] font-semibold text-[var(--muted)] uppercase tracking-wider">Account balance ($)</label>
                <input type="number" step="0.01" name="balance" value="{{ old('balance', auth()->user()->account_balance) }}" required class="bg-[var(--base)] border border-[var(--line)] text-[var(--text)] text-[13px] px-4 py-2.5 rounded-lg outline-none w-full hover:border-[var(--brand)]/50 focus:border-[var(--brand)] focus:ring-1 focus:ring-[var(--brand)] transition-colors num">
            </div>
            
            <div class="flex flex-col gap-2">
                <label class="text-[12px] font-semibold text-[var(--muted)] uppercase tracking-wider">Risk %</label>
                <input type="number" step="0.1" min="0.1" max="10" name="risk_pct" value="{{ old('risk_pct', auth()->user()->default_risk_pct) }}" required class="bg-[var(--base)] border border-[var(--line)] text-[var(--text)] text-[13px] px-4 py-2.5 rounded-lg outline-none w-full hover:border-[var(--brand)]/50 focus:border-[var(--brand)] focus:ring-1 focus:ring-[var(--brand)] transition-colors num">
            </div>
            
            <div class="flex flex-col gap-2">
                <label class="text-[12px] font-semibold text-[var(--muted)] uppercase tracking-wider">Entry price</label>
                <input type="number" step="any" name="entry" value="{{ old('entry') }}" required class="bg-[var(--base)] border border-[var(--line)] text-[var(--text)] text-[13px] px-4 py-2.5 rounded-lg outline-none w-full hover:border-[var(--brand)]/50 focus:border-[var(--brand)] focus:ring-1 focus:ring-[var(--brand)] transition-colors num">
            </div>
            
            <div class="flex flex-col gap-2">
                <label class="text-[12px] font-semibold text-[var(--muted)] uppercase tracking-wider">Stop-loss price</label>
                <input type="number" step="any" name="stop_loss" value="{{ old('stop_loss') }}" required class="bg-[var(--base)] border border-[var(--line)] text-[var(--down)] font-medium text-[13px] px-4 py-2.5 rounded-lg outline-none w-full hover:border-[var(--down)]/50 focus:border-[var(--down)] focus:ring-1 focus:ring-[var(--down)] transition-colors num">
            </div>
            
            <div class="sm:col-span-2 pt-2">
                <button type="submit" class="w-full inline-flex items-center justify-center gap-2.5 px-6 py-3 rounded-lg bg-[var(--brand)] text-white text-[13px] font-semibold hover:opacity-90 transition border-none cursor-pointer shadow-lg shadow-[var(--brand)]/20">
                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="4" y="4" width="16" height="16" rx="2" ry="2"></rect><rect x="9" y="9" width="6" height="6"></rect><line x1="9" y1="1" x2="9" y2="4"></line><line x1="15" y1="1" x2="15" y2="4"></line><line x1="9" y1="20" x2="9" y2="23"></line><line x1="15" y1="20" x2="15" y2="23"></line><line x1="20" y1="9" x2="23" y2="9"></line><line x1="20" y1="14" x2="23" y2="14"></line><line x1="1" y1="9" x2="4" y2="9"></line><line x1="1" y1="14" x2="4" y2="14"></line></svg>
                    Calculate position
                </button>
            </div>
        </form>
    </div>
    
    <div class="rounded-xl border border-[var(--line)] bg-[var(--surface)] p-6">
        @if($result)
            <div class="text-[11px] uppercase tracking-[0.14em] text-[var(--muted)] mb-4 font-semibold">Recommended position</div>
            <div class="text-[28px] font-bold num text-[var(--text)] mb-6 pb-6 border-b border-[var(--line)]">{{ $result['lot_size'] }} <span class="text-[16px] text-[var(--muted)] font-normal tracking-normal uppercase">lots</span></div>
            
            <div class="space-y-4 mb-6">
                <div class="flex justify-between items-center text-[13px]">
                    <span class="text-[var(--muted)]">Risk amount</span>
                    <strong class="num text-[var(--warn)]">${{ number_format($result['risk_amount'], 2) }}</strong>
                </div>
                <div class="flex justify-between items-center text-[13px]">
                    <span class="text-[var(--muted)]">Stop distance</span>
                    <strong class="num">{{ $result['stop_distance'] }}</strong>
                </div>
                <div class="flex justify-between items-center text-[13px]">
                    <span class="text-[var(--muted)]">Approx. pips/points</span>
                    <strong class="num">{{ $result['stop_pips'] }}</strong>
                </div>
            </div>
            
            <p class="text-[11px] text-[var(--muted)] m-0 leading-relaxed bg-[var(--base)] p-3 rounded-lg border border-[var(--line)]">
                <strong class="text-[var(--text)] block mb-1">Estimate only</strong>
                Verify contract size and pip value directly with your broker before execution.
            </p>
        @else
            <div class="flex flex-col items-center justify-center text-center h-full min-h-[200px] text-[var(--muted)]">
                <svg class="w-12 h-12 mb-4 text-[var(--line)]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round"><rect x="4" y="4" width="16" height="16" rx="2" ry="2"></rect><rect x="9" y="9" width="6" height="6"></rect><line x1="9" y1="1" x2="9" y2="4"></line><line x1="15" y1="1" x2="15" y2="4"></line><line x1="9" y1="20" x2="9" y2="23"></line><line x1="15" y1="20" x2="15" y2="23"></line><line x1="20" y1="9" x2="23" y2="9"></line><line x1="20" y1="14" x2="23" y2="14"></line><line x1="1" y1="9" x2="4" y2="9"></line><line x1="1" y1="14" x2="4" y2="14"></line></svg>
                <p class="text-[13px] leading-relaxed max-w-[200px] m-0">Enter balance, risk % and stop-loss to calculate the suggested lot size.</p>
            </div>
        @endif
    </div>
</div>
@endsection
