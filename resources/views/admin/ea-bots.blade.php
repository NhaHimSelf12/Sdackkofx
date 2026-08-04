@extends('layouts.app')
@section('title', 'EA Bots')
@section('subtitle', 'Automated paper-trading robots driven by the signal engine')

@section('content')
<div class="space-y-6">

    @if(session('status'))
        <div class="flex items-start gap-3 rounded-xl border border-[var(--brand)]/25 bg-[var(--brand)]/8 px-4 py-3.5">
            <svg class="w-4 h-4 text-[var(--brand)] mt-0.5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
            <p class="text-[13px] leading-relaxed text-[var(--brand)] m-0">{{ session('status') }}</p>
        </div>
    @endif

    <div class="flex items-start gap-3 rounded-xl border border-[var(--warn)]/25 bg-[var(--warn)]/8 px-4 py-3.5">
        <svg class="w-4 h-4 text-[var(--warn)] mt-0.5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path><line x1="12" y1="9" x2="12" y2="13"></line><line x1="12" y1="17" x2="12.01" y2="17"></line></svg>
        <p class="text-[13px] leading-relaxed text-[var(--warn)] m-0">Paper trading only — bots simulate execution against the verified market feed. No real broker orders are placed, and bots refuse to trade or settle on DEMO feed prices.</p>
    </div>

    <!-- Create Bot Form -->
    <section class="rounded-xl border border-[var(--line)] bg-[var(--surface)] overflow-hidden">
        <div class="px-5 py-4 border-b border-[var(--line)] bg-[var(--base)]/40">
            <h2 class="text-[14px] font-semibold m-0">Create a bot</h2>
        </div>
        <form method="POST" action="{{ route('ea.store') }}" class="p-5 space-y-6">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4">
                @foreach($modes as $key => $mode)
                <label class="relative flex flex-col gap-1.5 p-4 rounded-xl border-2 cursor-pointer transition-all {{ old('mode', 'daytrade') === $key ? 'border-[var(--brand)] bg-[var(--brand)]/5' : 'border-[var(--line)] hover:border-[var(--brand)]/50' }}">
                    <input type="radio" name="mode" value="{{ $key }}" class="peer sr-only" {{ old('mode', 'daytrade') === $key ? 'checked' : '' }} onchange="document.querySelectorAll('input[name=mode]').forEach(r => { r.closest('label').classList.remove('border-[var(--brand)]', 'bg-[var(--brand)]/5'); r.closest('label').classList.add('border-[var(--line)]'); if(r.checked) { r.closest('label').classList.add('border-[var(--brand)]', 'bg-[var(--brand)]/5'); r.closest('label').classList.remove('border-[var(--line)]'); } })">
                    
                    <div class="flex items-center justify-between mb-1">
                        <strong class="text-[13px] font-semibold text-[var(--text)]">{{ $mode['label'] }}</strong>
                        <div class="w-4 h-4 rounded-full border-2 flex items-center justify-center {{ old('mode', 'daytrade') === $key ? 'border-[var(--brand)]' : 'border-[var(--muted)]' }} peer-checked:border-[var(--brand)]">
                            <div class="w-2 h-2 rounded-full bg-[var(--brand)] opacity-0 transition-opacity {{ old('mode', 'daytrade') === $key ? 'opacity-100' : '' }} peer-checked:opacity-100"></div>
                        </div>
                    </div>
                    
                    <span class="text-[10px] font-bold tracking-wide uppercase px-2 py-0.5 rounded bg-[var(--base)] text-[var(--muted)] w-fit border border-[var(--line)]">
                        {{ $mode['timeframe'] }} · ≤ {{ $mode['max_per_day'] }}/day
                    </span>
                    
                    <p class="text-[12px] text-[var(--muted)] leading-relaxed mt-2 m-0 flex-1">{{ $mode['description'] }}</p>
                    
                    <div class="text-[10px] text-[var(--muted)] pt-2 border-t border-[var(--line)] mt-2">
                        {{ $mode['style'] === 'fast' ? 'Market entry' : 'Min '.$mode['min_confidence'].'% conf' }}{{ $mode['primary_only'] ? ' · PRIMARY only' : '' }}
                    </div>
                </label>
                @endforeach
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 pt-4 border-t border-[var(--line)]">
                <div class="flex flex-col gap-2">
                    <label class="text-[12px] font-semibold text-[var(--muted)] uppercase tracking-wider">Bot name</label>
                    <input type="text" name="name" value="{{ old('name') }}" placeholder="Gold Day Trader" required maxlength="60" class="bg-[var(--base)] border border-[var(--line)] text-[var(--text)] text-[13px] px-4 py-2.5 rounded-lg outline-none w-full hover:border-[var(--brand)]/50 focus:border-[var(--brand)] focus:ring-1 focus:ring-[var(--brand)] transition-colors">
                    @error('name')<span class="text-[11px] text-[var(--down)] font-medium">{{ $message }}</span>@enderror
                </div>
                
                <div class="flex flex-col gap-2">
                    <label class="text-[12px] font-semibold text-[var(--muted)] uppercase tracking-wider">Market</label>
                    <select name="market_id" class="bg-[var(--base)] border border-[var(--line)] text-[var(--text)] text-[13px] font-semibold px-4 py-2.5 rounded-lg outline-none cursor-pointer hover:border-[var(--brand)]/50 transition-colors w-full focus:border-[var(--brand)] focus:ring-1 focus:ring-[var(--brand)]">
                        <option value="">All markets</option>
                        @foreach($markets as $market)
                        <option value="{{ $market->id }}" {{ old('market_id') == $market->id ? 'selected' : '' }}>{{ $market->symbol }} · {{ $market->name }}</option>
                        @endforeach
                    </select>
                </div>
                
                <div class="flex flex-col gap-2">
                    <label class="text-[12px] font-semibold text-[var(--muted)] uppercase tracking-wider">Capital (USD $10–$5k)</label>
                    <input type="number" name="capital" value="{{ old('capital', 100) }}" min="10" max="5000" step="1" required class="bg-[var(--base)] border border-[var(--line)] text-[var(--text)] text-[13px] px-4 py-2.5 rounded-lg outline-none w-full hover:border-[var(--brand)]/50 focus:border-[var(--brand)] focus:ring-1 focus:ring-[var(--brand)] transition-colors num">
                    <span class="text-[10px] text-[var(--muted)] leading-tight">Small money → small positions. Sizing follows equity.</span>
                    @error('capital')<span class="text-[11px] text-[var(--down)] font-medium">{{ $message }}</span>@enderror
                </div>
                
                <div class="flex flex-col gap-2">
                    <label class="text-[12px] font-semibold text-[var(--muted)] uppercase tracking-wider">Risk per trade (%)</label>
                    <input type="number" name="risk_pct" value="{{ old('risk_pct', 2) }}" min="0.25" max="5" step="0.25" required class="bg-[var(--base)] border border-[var(--line)] text-[var(--text)] text-[13px] px-4 py-2.5 rounded-lg outline-none w-full hover:border-[var(--brand)]/50 focus:border-[var(--brand)] focus:ring-1 focus:ring-[var(--brand)] transition-colors num">
                    <span class="text-[10px] text-[var(--muted)] leading-tight">Risk amount = equity × risk %.</span>
                    @error('risk_pct')<span class="text-[11px] text-[var(--down)] font-medium">{{ $message }}</span>@enderror
                </div>
            </div>
            
            <div class="flex justify-end pt-2">
                <button type="submit" class="inline-flex items-center justify-center gap-2 px-6 py-2.5 rounded-lg bg-[var(--brand)] text-white text-[13px] font-semibold hover:opacity-90 transition border-none cursor-pointer shadow-lg shadow-[var(--brand)]/20">
                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                    Create bot
                </button>
            </div>
        </form>
    </section>

    <!-- Bots List -->
    <section class="rounded-xl border border-[var(--line)] bg-[var(--surface)] overflow-hidden">
        <div class="flex items-center justify-between px-5 py-4 border-b border-[var(--line)] bg-[var(--base)]/40">
            <h2 class="text-[14px] font-semibold m-0">Bots</h2>
            <span class="text-[11px] font-semibold px-2 py-0.5 rounded bg-[var(--raised)] border border-[var(--line)] text-[var(--muted)]">{{ $bots->count() }} configured</span>
        </div>
        <div class="overflow-x-auto thin-scroll">
            <table class="w-full text-[13px] min-w-[1000px] m-0 border-collapse">
                <thead>
                    <tr class="text-[10px] uppercase tracking-[0.12em] text-[var(--muted)] border-b border-[var(--line)]">
                        <th class="text-left font-medium px-5 py-3 border-none">Bot</th>
                        <th class="text-left font-medium px-3 py-3 border-none">Mode</th>
                        <th class="text-left font-medium px-3 py-3 border-none">Market</th>
                        <th class="text-right font-medium px-3 py-3 border-none">Capital</th>
                        <th class="text-right font-medium px-3 py-3 border-none">Equity</th>
                        <th class="text-right font-medium px-3 py-3 border-none">Today</th>
                        <th class="text-right font-medium px-3 py-3 border-none">Open</th>
                        <th class="text-right font-medium px-3 py-3 border-none">Trades</th>
                        <th class="text-right font-medium px-3 py-3 border-none">Win rate</th>
                        <th class="text-right font-medium px-3 py-3 border-none">PnL</th>
                        <th class="text-center font-medium px-3 py-3 border-none">Status</th>
                        <th class="text-right font-medium px-5 py-3 border-none">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[var(--line)]">
                @forelse($bots as $bot)
                    @php $mode = $modes[$bot->mode] ?? null; @endphp
                    <tr class="hover:bg-[var(--raised)]/50 transition-colors">
                        <td class="px-5 py-3.5 border-none">
                            <strong class="block text-[13px] font-semibold text-[var(--text)] mb-0.5">{{ $bot->name }}</strong>
                            <span class="block text-[10px] text-[var(--muted)] w-[180px] truncate" title="{{ $bot->last_note ?? 'Not run yet' }}">{{ $bot->last_note ?? 'Not run yet' }}</span>
                        </td>
                        <td class="px-3 py-3.5 border-none">
                            <span class="text-[10px] font-bold px-2 py-0.5 rounded border border-[var(--brand)]/25 bg-[var(--brand)]/10 text-[var(--brand)] uppercase tracking-wider">{{ $mode['label'] ?? $bot->mode }}</span>
                        </td>
                        <td class="px-3 py-3.5 text-[var(--muted)] font-medium border-none">{{ $bot->market?->symbol ?? 'All markets' }}</td>
                        <td class="num px-3 py-3.5 text-right border-none">
                            <span class="block">${{ number_format($bot->capital, 0) }}</span>
                            <span class="block text-[10px] text-[var(--muted)]">{{ $bot->tier() }}</span>
                        </td>
                        <td class="num px-3 py-3.5 text-right font-bold text-[14px] border-none">${{ number_format($bot->equity(), 2) }}</td>
                        <td class="num px-3 py-3.5 text-right text-[var(--muted)] border-none">{{ $bot->positions_today }}/{{ $mode['max_per_day'] ?? '-' }}</td>
                        <td class="num px-3 py-3.5 text-right font-semibold border-none">{{ $bot->open_trades_count }}</td>
                        <td class="num px-3 py-3.5 text-right text-[var(--muted)] border-none">{{ $bot->trades }}</td>
                        <td class="num px-3 py-3.5 text-right border-none">{{ $bot->winRate() }}%</td>
                        <td class="num px-3 py-3.5 text-right font-bold border-none {{ $bot->pnl >= 0 ? 'text-[var(--up)]' : 'text-[var(--down)]' }}">{{ $bot->pnl >= 0 ? '+' : '' }}{{ number_format($bot->pnl, 2) }}</td>
                        <td class="px-3 py-3.5 text-center border-none">
                            <span class="text-[10px] font-bold px-2 py-0.5 rounded border uppercase tracking-wider {{ $bot->status === 'running' ? 'border-[var(--up)]/25 bg-[var(--up)]/10 text-[var(--up)]' : 'border-[var(--warn)]/25 bg-[var(--warn)]/10 text-[var(--warn)]' }}">{{ $bot->status }}</span>
                        </td>
                        <td class="px-5 py-3.5 border-none">
                            <div class="flex items-center justify-end gap-1.5">
                                <form method="POST" action="{{ route('ea.run', $bot) }}" class="m-0">
                                    @csrf
                                    <button class="px-2.5 py-1.5 rounded-lg border border-[var(--line)] bg-[var(--raised)] text-[11px] font-semibold text-[var(--text)] hover:border-[var(--brand)] transition cursor-pointer">Run now</button>
                                </form>
                                <form method="POST" action="{{ route('ea.update', $bot) }}" class="m-0">
                                    @csrf @method('PATCH')
                                    <input type="hidden" name="status" value="{{ $bot->status === 'running' ? 'paused' : 'running' }}">
                                    <button class="px-2.5 py-1.5 rounded-lg border border-[var(--line)] bg-[var(--surface)] text-[11px] font-semibold text-[var(--muted)] hover:text-[var(--text)] hover:bg-[var(--raised)] transition cursor-pointer">{{ $bot->status === 'running' ? 'Pause' : 'Resume' }}</button>
                                </form>
                                <form method="POST" action="{{ route('ea.destroy', $bot) }}" onsubmit="return confirm('Delete this bot and its trade history?')" class="m-0">
                                    @csrf @method('DELETE')
                                    <button class="w-7 h-7 grid place-items-center rounded-lg border border-[var(--line)] bg-[var(--surface)] text-[var(--muted)] hover:text-[var(--down)] hover:bg-[var(--down)]/10 hover:border-[var(--down)]/30 transition cursor-pointer" title="Delete">
                                        <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="12" class="px-5 py-8 text-center text-[var(--muted)] border-none">No bots yet. Pick a mode above and create your first EA bot.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </section>

    <!-- Recent Trades -->
    <section class="rounded-xl border border-[var(--line)] bg-[var(--surface)] overflow-hidden">
        <div class="flex items-center justify-between px-5 py-4 border-b border-[var(--line)] bg-[var(--base)]/40">
            <h2 class="text-[14px] font-semibold m-0">Recent bot trades</h2>
            <span class="text-[11px] font-medium text-[var(--muted)]">paper execution log</span>
        </div>
        <div class="overflow-x-auto thin-scroll">
            <table class="w-full text-[13px] min-w-[1000px] m-0 border-collapse">
                <thead>
                    <tr class="text-[10px] uppercase tracking-[0.12em] text-[var(--muted)] border-b border-[var(--line)]">
                        <th class="text-left font-medium px-5 py-3 border-none">Bot</th>
                        <th class="text-left font-medium px-3 py-3 border-none">Market</th>
                        <th class="text-left font-medium px-3 py-3 border-none">Side</th>
                        <th class="text-right font-medium px-3 py-3 border-none">Entry</th>
                        <th class="text-right font-medium px-3 py-3 border-none">SL</th>
                        <th class="text-right font-medium px-3 py-3 border-none">TP</th>
                        <th class="text-right font-medium px-3 py-3 border-none">Units</th>
                        <th class="text-right font-medium px-3 py-3 border-none">Risk</th>
                        <th class="text-center font-medium px-3 py-3 border-none">Status</th>
                        <th class="text-right font-medium px-3 py-3 border-none">PnL</th>
                        <th class="text-right font-medium px-5 py-3 border-none">Opened</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[var(--line)]">
                @forelse($recentTrades as $trade)
                    <tr class="hover:bg-[var(--raised)]/50 transition-colors">
                        <td class="px-5 py-3.5 text-[var(--muted)] border-none">{{ $trade->bot?->name }}</td>
                        <td class="px-3 py-3.5 font-semibold text-[var(--text)] border-none">{{ $trade->market?->symbol }}</td>
                        <td class="px-3 py-3.5 border-none">
                            <span class="text-[10px] font-bold px-2 py-0.5 rounded border {{ $trade->direction === 'buy' ? 'border-[var(--up)]/25 bg-[var(--up)]/10 text-[var(--up)]' : 'border-[var(--down)]/25 bg-[var(--down)]/10 text-[var(--down)]' }} uppercase">{{ $trade->direction }}</span>
                        </td>
                        <td class="num px-3 py-3.5 text-right font-medium border-none">{{ number_format($trade->entry, $trade->market?->precision() ?? 2) }}</td>
                        <td class="num px-3 py-3.5 text-right text-[var(--down)] border-none">{{ number_format($trade->stop_loss, $trade->market?->precision() ?? 2) }}</td>
                        <td class="num px-3 py-3.5 text-right text-[var(--up)] border-none">{{ number_format($trade->take_profit, $trade->market?->precision() ?? 2) }}</td>
                        <td class="num px-3 py-3.5 text-right text-[var(--muted)] border-none">{{ rtrim(rtrim(number_format($trade->units, 4), '0'), '.') }}</td>
                        <td class="num px-3 py-3.5 text-right text-[var(--muted)] border-none">${{ number_format($trade->risk_amount, 2) }}</td>
                        <td class="px-3 py-3.5 text-center border-none">
                            @php
                                $statusClass = [
                                    'open' => 'border-[var(--brand)]/25 bg-[var(--brand)]/10 text-[var(--brand)]',
                                    'won' => 'border-[var(--up)]/25 bg-[var(--up)]/10 text-[var(--up)]',
                                    'lost' => 'border-[var(--down)]/25 bg-[var(--down)]/10 text-[var(--down)]'
                                ][$trade->status] ?? 'border-[var(--warn)]/25 bg-[var(--warn)]/10 text-[var(--warn)]';
                            @endphp
                            <span class="text-[10px] font-bold px-2 py-0.5 rounded border uppercase tracking-wider {{ $statusClass }}">{{ $trade->status }}</span>
                        </td>
                        <td class="num px-3 py-3.5 text-right font-bold border-none {{ ($trade->pnl ?? 0) >= 0 ? 'text-[var(--up)]' : 'text-[var(--down)]' }}">
                            {{ $trade->pnl === null ? '—' : (($trade->pnl >= 0 ? '+' : '').number_format($trade->pnl, 2)) }}
                        </td>
                        <td class="num px-5 py-3.5 text-right text-[11px] text-[var(--muted)] border-none">{{ $trade->opened_at->diffForHumans() }}</td>
                    </tr>
                @empty
                    <tr><td colspan="11" class="px-5 py-8 text-center text-[var(--muted)] border-none">No trades yet. Bots enter as soon as an eligible signal appears on a live/delayed feed.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </section>

</div>
@endsection
