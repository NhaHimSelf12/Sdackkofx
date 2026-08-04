@extends('layouts.app')

@section('title', 'Dashboard')
@section('subtitle', 'Last scan ' . optional($markets->max('analyzed_at'))?->diffForHumans())

@section('content')
<div class="space-y-6">

    @if(session('success'))
        <div class="flex items-start gap-3 rounded-xl border border-[var(--up)]/25 bg-[var(--up)]/8 px-4 py-3.5">
            <svg class="w-4 h-4 text-[var(--up)] mt-0.5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
            <p class="text-[13px] leading-relaxed text-[var(--up)] m-0">{{ session('success') }}</p>
        </div>
    @endif
    
    @if(session('warning'))
        <div class="flex items-start gap-3 rounded-xl border border-[var(--warn)]/25 bg-[var(--warn)]/8 px-4 py-3.5">
            <p class="text-[13px] leading-relaxed text-[var(--warn)] m-0">{{ session('warning') }}</p>
        </div>
    @endif

    @if($markets->contains(fn($m) => ($m->data_status ?? 'demo') === 'demo'))
    <div class="flex items-start gap-3 rounded-xl border border-[var(--warn)]/25 bg-[var(--warn)]/8 px-4 py-3.5">
        <svg class="w-4 h-4 text-[var(--warn)] mt-0.5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 9v4M12 17h.01M10.3 3.9L1.8 18a2 2 0 001.7 3h17a2 2 0 001.7-3L13.7 3.9a2 2 0 00-3.4 0z"/></svg>
        <p class="text-[13px] leading-relaxed text-[var(--warn)] m-0">
            <span class="font-semibold">Some markets are using DEMO prices.</span>
            Run <code class="num text-[11px] px-1.5 py-0.5 rounded bg-black/20 border border-[var(--warn)]/20">php artisan forex:feed-check --fresh</code> — do not trade from demo values.
        </p>
    </div>
    @endif

    <div class="ticker relative overflow-hidden rounded-xl border border-[var(--line)] bg-[var(--surface)]" aria-hidden="true">
        <div class="pointer-events-none absolute inset-y-0 left-0 w-16 bg-gradient-to-r from-[var(--surface)] to-transparent z-10"></div>
        <div class="pointer-events-none absolute inset-y-0 right-0 w-16 bg-gradient-to-l from-[var(--surface)] to-transparent z-10"></div>
        <div class="ticker-track py-3">
            @foreach ([1, 2] as $pass)
                <div class="flex shrink-0">
                    @foreach ($markets as $market)
                    @php $up = $market->change_pct >= 0; @endphp
                    <a href="{{ route('markets.show', $market) }}" class="flex items-center gap-2.5 px-5 border-r border-[var(--line)] whitespace-nowrap no-underline text-inherit hover:opacity-80 transition">
                        <span class="text-[12px] font-semibold">{{ $market->symbol }}</span>
                        <span class="num text-[12px] text-[var(--muted)]">{{ number_format($market->price, $market->precision()) }}</span>
                        <span class="num text-[11px] {{ $up ? 'text-[var(--up)]' : 'text-[var(--down)]' }}">{{ $up ? '▲' : '▼' }} {{ number_format(abs($market->change_pct), 2) }}%</span>
                    </a>
                    @endforeach
                </div>
            @endforeach
        </div>
    </div>

    <section class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
        <div class="rounded-xl border border-[var(--line)] bg-[var(--surface)] p-5">
            <div class="flex items-start justify-between mb-4">
                <span class="text-[11px] font-medium uppercase tracking-[0.14em] text-[var(--muted)]">Markets tracked</span>
                <span class="w-8 h-8 rounded-lg bg-[var(--brand)]/12 text-[var(--brand)] grid place-items-center">
                    <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="12" cy="12" r="9"/><path d="M3 12h18M12 3a15 15 0 010 18a15 15 0 010-18"/></svg>
                </span>
            </div>
            <p class="num text-3xl font-bold leading-none mb-3 m-0">{{ $stats['markets'] }}</p>
            <div class="flex items-center gap-3 text-[11px] mt-2">
                <span class="inline-flex items-center gap-1.5 text-[var(--up)]"><span class="w-1.5 h-1.5 rounded-full bg-[var(--up)]"></span>{{ $stats['bullish_markets'] }} bullish</span>
                <span class="inline-flex items-center gap-1.5 text-[var(--down)]"><span class="w-1.5 h-1.5 rounded-full bg-[var(--down)]"></span>{{ $stats['bearish_markets'] }} bearish</span>
            </div>
        </div>

        <div class="rounded-xl border border-[var(--line)] bg-[var(--surface)] p-5">
            <div class="flex items-start justify-between mb-4">
                <span class="text-[11px] font-medium uppercase tracking-[0.14em] text-[var(--muted)]">Active signals</span>
                <span class="w-8 h-8 rounded-lg bg-[var(--raised)] grid place-items-center text-[var(--muted)]">
                    <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"><path d="M13 2L4.5 13.5H11l-1 8.5 8.5-11.5H12l1-8.5z"/></svg>
                </span>
            </div>
            <p class="num text-3xl font-bold leading-none mb-3 m-0">{{ $stats['active_signals'] }}</p>
            <p class="text-[11px] text-[var(--muted)] m-0 mt-2">Across all strategies</p>
        </div>

        <div class="rounded-xl border border-[var(--line)] bg-[var(--surface)] p-5">
            <div class="flex items-start justify-between mb-4">
                <span class="text-[11px] font-medium uppercase tracking-[0.14em] text-[var(--muted)]">Buy entries</span>
                <span class="w-8 h-8 rounded-lg bg-[var(--up)]/12 text-[var(--up)] grid place-items-center">
                    <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M3 17l6-6 4 4 8-8"/><path d="M21 7v5h-5"/></svg>
                </span>
            </div>
            <p class="num text-3xl font-bold leading-none text-[var(--up)] mb-3 m-0">{{ $stats['buy_signals'] }}</p>
            @php $buyPct = $stats['active_signals'] ? round(($stats['buy_signals'] / $stats['active_signals']) * 100) : 0; @endphp
            <div class="h-1 rounded-full bg-[var(--raised)] overflow-hidden mt-3"><div class="h-full bg-[var(--up)] rounded-full" style="width:{{ $buyPct }}%"></div></div>
            <p class="text-[11px] text-[var(--muted)] mt-2 mb-0">{{ $buyPct }}% of open opportunities</p>
        </div>

        <div class="rounded-xl border border-[var(--line)] bg-[var(--surface)] p-5">
            <div class="flex items-start justify-between mb-4">
                <span class="text-[11px] font-medium uppercase tracking-[0.14em] text-[var(--muted)]">Sell entries</span>
                <span class="w-8 h-8 rounded-lg bg-[var(--down)]/12 text-[var(--down)] grid place-items-center">
                    <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M3 7l6 6 4-4 8 8"/><path d="M21 17v-5h-5"/></svg>
                </span>
            </div>
            <p class="num text-3xl font-bold leading-none text-[var(--down)] mb-3 m-0">{{ $stats['sell_signals'] }}</p>
            @php $sellPct = $stats['active_signals'] ? round(($stats['sell_signals'] / $stats['active_signals']) * 100) : 0; @endphp
            <div class="h-1 rounded-full bg-[var(--raised)] overflow-hidden mt-3"><div class="h-full bg-[var(--down)] rounded-full" style="width:{{ $sellPct }}%"></div></div>
            <p class="text-[11px] text-[var(--muted)] mt-2 mb-0">{{ $sellPct }}% of open opportunities</p>
        </div>
    </section>

    <section>
        <div class="flex items-center gap-2.5 mb-4">
            <h2 class="text-[13px] font-semibold uppercase tracking-[0.14em] m-0">Markets</h2>
            <span class="num text-[11px] px-2 py-0.5 rounded-md bg-[var(--raised)] text-[var(--muted)] border border-[var(--line)]">{{ $markets->count() }}</span>
        </div>
        <div class="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-4 gap-3">
            @foreach ($markets as $market)
            @php 
                $up = $market->change_pct >= 0; 
                $biasClass = $market->ai_bias === 'bullish' ? 'text-[var(--up)] bg-[var(--up)]/12 border-[var(--up)]/20'
                           : ($market->ai_bias === 'bearish' ? 'text-[var(--down)] bg-[var(--down)]/12 border-[var(--down)]/20'
                           : 'text-[var(--muted)] bg-[var(--raised)] border-[var(--line)]');
            @endphp
            <a href="{{ route('markets.show', $market) }}" class="rounded-xl border border-[var(--line)] bg-[var(--surface)] p-4 hover:border-[var(--brand)]/40 transition-colors block no-underline text-inherit">
                <div class="flex items-start justify-between gap-2 mb-3">
                    <div class="min-w-0">
                        <p class="text-[13px] font-semibold truncate m-0">{{ $market->symbol }}</p>
                        <p class="text-[11px] text-[var(--muted)] truncate m-0">{{ $market->name }}</p>
                    </div>
                    <span class="shrink-0 text-[9px] font-semibold tracking-wider px-1.5 py-0.5 rounded border {{ $market->data_status === 'live' ? 'text-[var(--up)] border-[var(--up)]/25 bg-[var(--up)]/10' : 'text-[var(--warn)] border-[var(--warn)]/25 bg-[var(--warn)]/10' }}">{{ strtoupper($market->data_status ?? 'demo') }}</span>
                </div>
                <p class="num text-[19px] font-bold leading-none mb-2.5 m-0">{{ number_format($market->price, $market->precision()) }}</p>
                <div class="flex flex-wrap items-center gap-2 mt-2">
                    <span class="num text-[11px] font-medium {{ $up ? 'text-[var(--up)]' : 'text-[var(--down)]' }}">{{ $up ? '+' : '' }}{{ number_format($market->change_pct, 2) }}%</span>
                    <span class="text-[10px] px-1.5 py-0.5 rounded border capitalize {{ $biasClass }}">{{ $market->ai_bias ?? 'neutral' }}</span>
                </div>
            </a>
            @endforeach
        </div>
    </section>

    <section class="grid grid-cols-1 xl:grid-cols-3 gap-5 pt-2">
        <div class="xl:col-span-2 rounded-xl border border-[var(--line)] bg-[var(--surface)] overflow-hidden min-w-0">
            <div class="px-5 py-4 border-b border-[var(--line)] flex justify-between items-center">
                <h2 class="text-[13px] font-semibold m-0">Top entry signals</h2>
                <a href="{{ route('signals.index') }}" class="text-[11px] text-[var(--brand)] hover:underline no-underline">View all</a>
            </div>
            <div class="overflow-x-auto thin-scroll">
                <table class="w-full text-[13px] min-w-[720px] m-0 border-collapse">
                    <thead>
                        <tr class="text-[10px] uppercase tracking-[0.12em] text-[var(--muted)] border-b border-[var(--line)] bg-[var(--base)]/40">
                            <th class="text-left font-medium px-5 py-3 border-none">Market</th>
                            <th class="text-left font-medium px-3 py-3 border-none">Strategy</th>
                            <th class="text-left font-medium px-3 py-3 border-none">Side</th>
                            <th class="text-right font-medium px-3 py-3 border-none">Entry</th>
                            <th class="text-right font-medium px-3 py-3 border-none">SL</th>
                            <th class="text-right font-medium px-3 py-3 border-none">TP</th>
                            <th class="text-right font-medium px-3 py-3 border-none">R:R</th>
                            <th class="text-right font-medium px-5 py-3 border-none">Conf.</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[var(--line)]">
                        @forelse ($signals as $signal)
                        @php $buy = $signal->direction === 'buy'; @endphp
                        <tr class="hover:bg-[var(--raised)]/50 transition-colors">
                            <td class="px-5 py-3.5 font-semibold border-none"><a href="{{ route('markets.show', $signal->market) }}" class="text-inherit hover:text-[var(--brand)] no-underline">{{ $signal->market->symbol }}</a></td>
                            <td class="px-3 py-3.5 border-none">
                                <span class="text-[11px] px-2 py-0.5 rounded border border-[var(--brand)]/25 bg-[var(--brand)]/10 text-[var(--brand)]">{{ $signal->strategy }}</span>
                                @if($signal->is_primary) <span class="text-[11px] px-1 py-0.5 rounded border border-[var(--warn)]/25 bg-[var(--warn)]/10 text-[var(--warn)] ml-1">★</span> @endif
                            </td>
                            <td class="px-3 py-3.5 border-none"><span class="text-[11px] font-semibold px-2 py-0.5 rounded border {{ $buy ? 'text-[var(--up)] border-[var(--up)]/25 bg-[var(--up)]/10' : 'text-[var(--down)] border-[var(--down)]/25 bg-[var(--down)]/10' }}">{{ strtoupper($signal->direction) }}</span></td>
                            <td class="num px-3 py-3.5 text-right border-none">{{ number_format($signal->entry, $signal->market->precision()) }}</td>
                            <td class="num px-3 py-3.5 text-right text-[var(--down)] border-none">{{ number_format($signal->stop_loss, $signal->market->precision()) }}</td>
                            <td class="num px-3 py-3.5 text-right text-[var(--up)] border-none font-medium">{{ number_format($signal->take_profit, $signal->market->precision()) }}</td>
                            <td class="num px-3 py-3.5 text-right font-semibold border-none">{{ number_format($signal->risk_reward, 1) }}</td>
                            <td class="px-5 py-3.5 text-right border-none">
                                <span class="inline-flex items-center gap-2">
                                    <span class="w-10 h-1 rounded-full bg-[var(--raised)] overflow-hidden"><span class="block h-full rounded-full {{ $buy ? 'bg-[var(--up)]' : 'bg-[var(--down)]' }}" style="width:{{ $signal->confidence }}%"></span></span>
                                    <span class="num text-[11px] text-[var(--muted)] w-8 text-right">{{ $signal->confidence }}%</span>
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="px-5 py-6 text-center text-[var(--muted)] border-none">No active signals — run <code>php artisan forex:scan</code>.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="rounded-xl border border-[var(--line)] bg-[var(--surface)] min-w-0">
            <div class="px-5 py-4 border-b border-[var(--line)] flex justify-between items-center">
                <h2 class="text-[13px] font-semibold m-0">News & sentiment</h2>
                <a href="{{ route('news.index') }}" class="text-[11px] text-[var(--brand)] hover:underline no-underline">View all</a>
            </div>
            <div class="divide-y divide-[var(--line)]">
                @forelse ($news as $item)
                @php
                    $biasClass = $item->sentiment === 'bullish' ? 'text-[var(--up)] bg-[var(--up)]/12 border-[var(--up)]/20'
                               : ($item->sentiment === 'bearish' ? 'text-[var(--down)] bg-[var(--down)]/12 border-[var(--down)]/20'
                               : 'text-[var(--muted)] bg-[var(--raised)] border-[var(--line)]');
                @endphp
                <article class="px-5 py-4 hover:bg-[var(--raised)]/40 transition-colors">
                    <h3 class="text-[13px] font-medium leading-snug mb-2.5 mt-0">{{ $item->title }}</h3>
                    <div class="flex flex-wrap items-center gap-2 text-[11px] text-[var(--muted)]">
                        <span class="px-2 py-0.5 rounded border capitalize {{ $biasClass }}">{{ $item->sentiment }}</span>
                        @if ($item->impact === 'high')<span class="px-2 py-0.5 rounded border border-[var(--warn)]/25 bg-[var(--warn)]/10 text-[var(--warn)]">High impact</span>@endif
                        <span class="text-[var(--text)]/70">{{ $item->source }}</span>
                        <span>· {{ optional($item->published_at)?->diffForHumans() }}</span>
                    </div>
                </article>
                @empty
                <p class="px-5 py-6 text-center text-[var(--muted)] m-0">No news yet.</p>
                @endforelse
            </div>
        </div>
    </section>

</div>
@endsection
