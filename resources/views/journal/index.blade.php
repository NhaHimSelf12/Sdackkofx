@extends('layouts.app')

@section('title', 'Trading Journal')
@section('subtitle', 'Measure performance and trading discipline')

@section('content')
<div class="space-y-6">

    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div class="flex flex-wrap items-center gap-2">
            <span class="px-3 py-1.5 rounded-full text-[11px] font-medium border bg-[var(--text)] text-[var(--base)] border-[var(--text)]">All trades</span>
        </div>
        <a href="{{ route('journal.create') }}" class="h-9 inline-flex items-center gap-2.5 px-4 rounded-lg bg-[var(--brand)] text-white text-[12px] font-semibold hover:opacity-90 transition border-none cursor-pointer no-underline m-0">
            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
            Plan a trade
        </a>
    </div>

    @if(session('success'))
        <div class="flex items-start gap-3 rounded-xl border border-[var(--up)]/25 bg-[var(--up)]/8 px-4 py-3.5">
            <svg class="w-4 h-4 text-[var(--up)] mt-0.5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
            <p class="text-[13px] leading-relaxed text-[var(--up)] m-0">{{ session('success') }}</p>
        </div>
    @endif

    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 md:gap-5">
        <div class="rounded-xl border border-[var(--line)] bg-[var(--surface)] p-5">
            <div class="text-[11px] uppercase tracking-[0.12em] text-[var(--muted)] mb-2">Closed trades</div>
            <div class="text-2xl font-bold num">{{ $stats['total'] }}</div>
        </div>
        <div class="rounded-xl border border-[var(--line)] bg-[var(--surface)] p-5">
            <div class="text-[11px] uppercase tracking-[0.12em] text-[var(--muted)] mb-2">Win rate</div>
            <div class="text-2xl font-bold num">{{ $stats['win_rate'] }}%</div>
        </div>
        <div class="rounded-xl border border-[var(--line)] bg-[var(--surface)] p-5">
            <div class="text-[11px] uppercase tracking-[0.12em] text-[var(--muted)] mb-2">Net P/L</div>
            <div class="text-2xl font-bold num {{ $stats['net_pnl'] >= 0 ? 'text-[var(--up)]' : 'text-[var(--down)]' }}">${{ number_format($stats['net_pnl'], 2) }}</div>
        </div>
        <div class="rounded-xl border border-[var(--line)] bg-[var(--surface)] p-5">
            <div class="text-[11px] uppercase tracking-[0.12em] text-[var(--muted)] mb-2">Average R</div>
            <div class="text-2xl font-bold num">{{ number_format($stats['avg_r'], 2) }}R</div>
        </div>
    </div>

    <section>
        <div class="flex items-center gap-2.5 mb-4 mt-8">
            <h2 class="text-[13px] font-semibold uppercase tracking-[0.14em] m-0">Trade history</h2>
        </div>
        
        <div class="rounded-xl border border-[var(--line)] bg-[var(--surface)] overflow-hidden min-w-0">
            <div class="overflow-x-auto thin-scroll">
                <table class="w-full text-[13px] min-w-[800px] m-0 border-collapse">
                    <thead>
                        <tr class="text-[10px] uppercase tracking-[0.12em] text-[var(--muted)] border-b border-[var(--line)] bg-[var(--base)]/40">
                            <th class="text-left font-medium px-5 py-3 border-none">Market</th>
                            <th class="text-left font-medium px-3 py-3 border-none">Side</th>
                            <th class="text-left font-medium px-3 py-3 border-none">Strategy</th>
                            <th class="text-right font-medium px-3 py-3 border-none">Entry</th>
                            <th class="text-right font-medium px-3 py-3 border-none">Risk</th>
                            <th class="text-right font-medium px-3 py-3 border-none">Lot</th>
                            <th class="text-left font-medium px-3 py-3 border-none">Status</th>
                            <th class="text-right font-medium px-3 py-3 border-none">P/L</th>
                            <th class="text-right font-medium px-5 py-3 border-none">Date</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[var(--line)]">
                        @forelse($trades as $trade)
                        <tr class="hover:bg-[var(--raised)]/50 transition-colors">
                            <td class="px-5 py-3.5 font-semibold border-none">{{ $trade->market->symbol }}</td>
                            <td class="px-3 py-3.5 border-none">
                                <span class="text-[11px] font-semibold px-2 py-0.5 rounded border {{ $trade->direction === 'buy' ? 'text-[var(--up)] border-[var(--up)]/25 bg-[var(--up)]/10' : 'text-[var(--down)] border-[var(--down)]/25 bg-[var(--down)]/10' }}">{{ strtoupper($trade->direction) }}</span>
                            </td>
                            <td class="px-3 py-3.5 text-[var(--muted)] border-none">{{ $trade->strategy ?: '—' }}</td>
                            <td class="num px-3 py-3.5 text-right border-none">{{ number_format($trade->entry, $trade->market->precision()) }}</td>
                            <td class="num px-3 py-3.5 text-right border-none">${{ number_format($trade->risk_amount, 2) }}</td>
                            <td class="num px-3 py-3.5 text-right border-none">{{ $trade->lot_size }}</td>
                            <td class="px-3 py-3.5 border-none">
                                <span class="text-[10px] px-1.5 py-0.5 rounded border text-[var(--muted)] border-[var(--line)] bg-[var(--raised)]">{{ ucfirst($trade->status) }}</span>
                            </td>
                            <td class="num px-3 py-3.5 text-right font-semibold border-none {{ ($trade->profit_loss ?? 0) >= 0 ? 'text-[var(--up)]' : 'text-[var(--down)]' }}">{{ $trade->profit_loss === null ? '—' : '$'.number_format($trade->profit_loss, 2) }}</td>
                            <td class="num px-5 py-3.5 text-right text-[12px] text-[var(--muted)] border-none">{{ $trade->created_at->format('d M Y') }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="px-5 py-6 text-center text-[var(--muted)] border-none">No trades yet. Start by planning your first trade.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </section>
</div>
@endsection
