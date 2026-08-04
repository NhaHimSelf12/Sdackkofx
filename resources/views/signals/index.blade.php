@extends('layouts.app')

@section('title', 'Signals')
@section('subtitle', 'Entry signals from all strategies')

@push('styles')
<style>
  .sig-table thead th { position:sticky; top:0; z-index:2; background:var(--raised); }
  .sig-table .col-sticky { position:sticky; left:0; z-index:1; background:var(--surface); }
  .sig-table thead .col-sticky { z-index:3; background:var(--raised); }
  .sig-table tbody tr:hover .col-sticky { background:var(--raised); }
  .sig-table .col-sticky::after { content:''; position:absolute; top:0; right:0; bottom:0; width:1px; background:var(--line); }

  .row-expired { opacity:.5; }
</style>
@endpush

@section('content')
<div class="space-y-5">

  <!-- Header Actions -->
  <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
    <div class="inline-flex items-center gap-2.5 px-3.5 h-9 rounded-lg bg-[var(--surface)] border border-[var(--line)] w-fit">
      <span class="relative w-1.5 h-1.5 rounded-full bg-[var(--up)] text-[var(--up)] pulse"></span>
      <span class="text-[11px] font-medium text-[var(--up)]">Only verified remote feeds generate signals</span>
    </div>
    <form method="POST" action="{{ route('signals.refresh') }}" class="m-0 flex">
      @csrf
      <button type="submit" class="h-9 inline-flex items-center justify-center gap-2 px-4 rounded-lg bg-[var(--brand)] text-white text-[12px] font-semibold hover:opacity-90 transition border-none cursor-pointer">
        <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 12a9 9 0 11-3-6.7"/><path d="M21 3v6h-6"/></svg>
        Refresh signals
      </button>
    </form>
  </div>

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

  @php
    $items = $signals->items();
    $totalShown = count($items);
    $totalSignals = $signals->total();
    $buyCount = collect($items)->where('direction', 'buy')->count();
    $sellCount = collect($items)->where('direction', 'sell')->count();
    $buyPct = $totalShown > 0 ? ($buyCount / $totalShown) * 100 : 0;
    $sellPct = $totalShown > 0 ? ($sellCount / $totalShown) * 100 : 0;
    $avgConf = $totalShown > 0 ? collect($items)->avg('confidence') : 0;
    $avgRR = $totalShown > 0 ? collect($items)->avg('risk_reward') : 0;
  @endphp

  <!-- Stats Grid -->
  <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
    <div class="rounded-xl border border-[var(--line)] bg-[var(--surface)] px-4 py-3.5">
      <p class="text-[10px] font-semibold uppercase tracking-[0.14em] text-[var(--muted)] mb-2 m-0">Signals shown</p>
      <p class="num text-[22px] font-bold leading-none m-0 mt-2">{{ $totalShown }} <span class="text-[11px] font-medium text-[var(--muted)]">of {{ $totalSignals }}</span></p>
    </div>
    <div class="rounded-xl border border-[var(--line)] bg-[var(--surface)] px-4 py-3.5">
      <p class="text-[10px] font-semibold uppercase tracking-[0.14em] text-[var(--muted)] mb-2 m-0">Buy / Sell</p>
      <div class="flex items-baseline gap-1.5 mb-2 mt-2">
        <span class="num text-[22px] font-bold leading-none text-[var(--up)]">{{ $buyCount }}</span>
        <span class="text-[var(--muted)] text-[13px]">/</span>
        <span class="num text-[22px] font-bold leading-none text-[var(--down)]">{{ $sellCount }}</span>
      </div>
      <div class="flex h-1 rounded-full overflow-hidden bg-[var(--raised)]">
        <div class="bg-[var(--up)]" style="width:{{ $buyPct }}%"></div><div class="bg-[var(--down)]" style="width:{{ $sellPct }}%"></div>
      </div>
    </div>
    <div class="rounded-xl border border-[var(--line)] bg-[var(--surface)] px-4 py-3.5">
      <p class="text-[10px] font-semibold uppercase tracking-[0.14em] text-[var(--muted)] mb-2 m-0">Avg confidence</p>
      <p class="num text-[22px] font-bold leading-none mb-2 mt-2">{{ number_format($avgConf, 0) }}%</p>
      <div class="h-1 rounded-full bg-[var(--raised)] overflow-hidden"><div class="h-full bg-[var(--brand)] rounded-full" style="width:{{ $avgConf }}%"></div></div>
    </div>
    <div class="rounded-xl border border-[var(--line)] bg-[var(--surface)] px-4 py-3.5">
      <p class="text-[10px] font-semibold uppercase tracking-[0.14em] text-[var(--muted)] mb-2 m-0">Avg risk : reward</p>
      <p class="num text-[22px] font-bold leading-none m-0 mt-2">1 : {{ number_format($avgRR, 1) }}</p>
    </div>
  </div>

  <!-- Filters -->
  <div class="rounded-xl border border-[var(--line)] bg-[var(--surface)] px-4 py-3 flex flex-wrap items-center gap-x-6 gap-y-3">
    <div class="flex items-center gap-2">
      <span class="text-[10px] font-semibold uppercase tracking-[0.14em] text-[var(--muted)]">Side</span>
      <div class="flex items-center gap-1 p-1 rounded-lg bg-[var(--raised)] border border-[var(--line)]">
        <a href="{{ route('signals.index') }}" class="h-7 px-3 grid place-items-center rounded-md text-[11px] font-semibold no-underline transition {{ !request('direction') ? 'bg-[var(--text)] text-[var(--base)]' : 'text-[var(--muted)] hover:text-[var(--text)]' }}">All</a>
        <a href="{{ route('signals.index', ['direction' => 'buy'] + request()->except('direction')) }}" class="h-7 px-3 grid place-items-center rounded-md text-[11px] font-semibold no-underline transition {{ request('direction') === 'buy' ? 'bg-[var(--text)] text-[var(--base)]' : 'text-[var(--muted)] hover:text-[var(--up)]' }}">Buy</a>
        <a href="{{ route('signals.index', ['direction' => 'sell'] + request()->except('direction')) }}" class="h-7 px-3 grid place-items-center rounded-md text-[11px] font-semibold no-underline transition {{ request('direction') === 'sell' ? 'bg-[var(--text)] text-[var(--base)]' : 'text-[var(--muted)] hover:text-[var(--down)]' }}">Sell</a>
      </div>
    </div>

    <div class="flex items-center gap-2">
      <span class="text-[10px] font-semibold uppercase tracking-[0.14em] text-[var(--muted)]">Strategy</span>
      <div class="flex flex-wrap items-center gap-1.5">
        @foreach(['SMC', 'ICT', 'MSNR', 'TECH'] as $strat)
        <a href="{{ route('signals.index', ['strategy' => $strat] + request()->except('strategy')) }}" class="h-7 px-3 inline-flex items-center rounded-full text-[11px] font-medium no-underline transition {{ request('strategy') === $strat ? 'border border-[var(--brand)] bg-[var(--brand)] text-white' : 'border border-[var(--line)] bg-[var(--raised)] text-[var(--muted)] hover:text-[var(--text)] hover:border-[var(--brand)]/40' }}">{{ $strat }}</a>
        @endforeach
      </div>
    </div>

    <div class="flex items-center gap-2">
      <span class="text-[10px] font-semibold uppercase tracking-[0.14em] text-[var(--muted)]">Status</span>
      <a href="{{ route('signals.index', ['status' => 'expired'] + request()->except('status')) }}" class="h-7 px-3 inline-flex items-center rounded-full text-[11px] font-medium no-underline transition {{ request('status') === 'expired' ? 'border border-[var(--text)] bg-[var(--text)] text-[var(--base)]' : 'border border-[var(--line)] bg-[var(--raised)] text-[var(--muted)] hover:text-[var(--text)]' }}">Expired</a>
    </div>

    <a href="{{ route('signals.index') }}" class="ml-auto inline-flex items-center gap-1.5 text-[11px] font-medium text-[var(--muted)] hover:text-[var(--down)] no-underline transition">
      <svg class="w-3 h-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"><path d="M18 6L6 18M6 6l12 12"/></svg>
      Clear filters
    </a>
  </div>

  <!-- Desktop Table -->
  <div class="hidden md:block rounded-xl border border-[var(--line)] bg-[var(--surface)] overflow-hidden">
    <div class="overflow-x-auto thin-scroll max-h-[70vh]">
      <table class="sig-table w-full text-[13px] min-w-[1080px] border-collapse m-0">
        <thead>
          <tr class="text-[10px] uppercase tracking-[0.12em] text-[var(--muted)]">
            <th class="col-sticky text-left font-semibold px-5 py-3 border-none">Market</th>
            <th class="text-left font-semibold px-3 py-3 border-none">Strategy</th>
            <th class="text-left font-semibold px-3 py-3 border-none">Side</th>
            <th class="text-right font-semibold px-3 py-3 border-none">Entry</th>
            <th class="text-right font-semibold px-3 py-3 border-none">Stop loss</th>
            <th class="text-right font-semibold px-3 py-3 border-none">Targets</th>
            <th class="text-right font-semibold px-3 py-3 border-none">R:R</th>
            <th class="text-right font-semibold px-3 py-3 border-none">Confidence</th>
            <th class="text-left font-semibold px-3 py-3 border-none">Feed / Expiry</th>
            <th class="text-right font-semibold px-5 py-3 border-none">Action</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-[var(--line)]">
          @forelse ($signals as $i => $signal)
          @php 
            $buy = $signal->direction === 'buy'; 
            $expired = optional($signal->expires_at)->isPast();
            $feedClass = $signal->data_status === 'live' ? 'text-[var(--up)] border-[var(--up)]/25 bg-[var(--up)]/10' : 'text-[var(--warn)] border-[var(--warn)]/25 bg-[var(--warn)]/10';
            $feedLabel = strtoupper($signal->data_status ?? 'unknown') . (($signal->data_source ?? '') === 'yahoo' ? '' : ' · ' . strtoupper($signal->data_source ?? 'unknown'));
          @endphp
          <tr class="hover:bg-[var(--raised)]/50 transition-colors {{ $expired ? 'row-expired' : '' }}">
            <td class="col-sticky px-5 py-3.5 border-none">
              <a href="{{ route('markets.show', $signal->market) }}" class="block no-underline text-inherit group m-0">
                <span class="block font-semibold text-[13px] group-hover:text-[var(--brand)] transition m-0">{{ $signal->market->symbol }}</span>
                <span class="num block text-[10px] text-[var(--muted)] mt-0.5 m-0">{{ $signal->timeframe }}</span>
              </a>
            </td>
            <td class="px-3 py-3.5 whitespace-nowrap border-none">
              <span class="text-[11px] px-2 py-0.5 rounded border border-[var(--brand)]/25 bg-[var(--brand)]/10 text-[var(--brand)]">{{ $signal->strategy }}</span>
              @if($signal->is_primary) <span class="text-[11px] px-1 py-0.5 rounded border border-[var(--warn)]/25 bg-[var(--warn)]/10 text-[var(--warn)] ml-1" title="Primary trade plan for this market">★</span> @endif
            </td>
            <td class="px-3 py-3.5 border-none"><span class="text-[11px] font-bold px-2 py-0.5 rounded border {{ $buy ? 'text-[var(--up)] border-[var(--up)]/25 bg-[var(--up)]/10' : 'text-[var(--down)] border-[var(--down)]/25 bg-[var(--down)]/10' }}">{{ strtoupper($signal->direction) }}</span></td>
            <td class="num px-3 py-3.5 text-right font-semibold border-none">{{ number_format($signal->entry, $signal->market->precision()) }}</td>
            <td class="num px-3 py-3.5 text-right text-[var(--down)] border-none">{{ number_format($signal->stop_loss, $signal->market->precision()) }}</td>
            <td class="px-3 py-3.5 text-right border-none">
              <div class="num inline-grid grid-cols-[auto_auto] gap-x-2 gap-y-0.5 text-[11px] text-right m-0">
                <span class="text-[var(--muted)] text-left m-0">TP1</span><span class="text-[var(--up)]/70 m-0">{{ number_format($signal->tp1, $signal->market->precision()) }}</span>
                <span class="text-[var(--muted)] text-left m-0">TP2</span><span class="text-[var(--up)]/70 m-0">{{ number_format($signal->tp2, $signal->market->precision()) }}</span>
                <span class="text-[var(--muted)] text-left m-0">TP3</span><span class="text-[var(--up)] font-semibold m-0">{{ number_format($signal->take_profit, $signal->market->precision()) }}</span>
              </div>
            </td>
            <td class="num px-3 py-3.5 text-right font-semibold border-none">{{ number_format($signal->risk_reward, 1) }}</td>
            <td class="px-3 py-3.5 border-none">
              <div class="flex items-center justify-end gap-2 m-0">
                <span class="w-12 h-1 rounded-full bg-[var(--raised)] overflow-hidden"><span class="block h-full rounded-full {{ $buy ? 'bg-[var(--up)]' : 'bg-[var(--down)]' }}" style="width:{{ $signal->confidence }}%"></span></span>
                <span class="num text-[11px] font-semibold w-8 text-right">{{ $signal->confidence }}%</span>
              </div>
            </td>
            <td class="px-3 py-3.5 whitespace-nowrap border-none">
              <span class="block text-[9px] font-bold tracking-wider px-1.5 py-0.5 rounded border w-fit m-0 {{ $feedClass }}">{{ $feedLabel }}</span>
              <span class="block text-[10px] text-[var(--muted)] mt-1 m-0">{{ $expired ? 'Expired' : (optional($signal->expires_at)?->diffForHumans() ?? '—') }}</span>
            </td>
            <td class="px-5 py-3.5 border-none">
              <div class="flex items-center justify-end gap-1.5 m-0">
                <button type="button" class="js-copy inline-flex items-center justify-center gap-1.5 h-7 px-2.5 rounded-lg border border-[var(--line)] bg-[var(--raised)] text-[11px] font-semibold hover:border-[var(--brand)] transition whitespace-nowrap cursor-pointer" data-text="{{ $signal->direction === 'buy' ? 'BUY' : 'SELL' }} {{ $signal->market->symbol }} \nEntry: {{ number_format($signal->entry, $signal->market->precision()) }} \nSL: {{ number_format($signal->stop_loss, $signal->market->precision()) }} \nTP1: {{ number_format($signal->tp1, $signal->market->precision()) }} \nTP2: {{ number_format($signal->tp2, $signal->market->precision()) }} \nTP3: {{ number_format($signal->take_profit, $signal->market->precision()) }}">
                  <svg class="w-3 h-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="9" y="9" width="11" height="11" rx="2"/><path d="M5 15V5a2 2 0 012-2h10"/></svg>
                  Copy
                </button>
                <button type="button" class="js-reason w-7 h-7 grid place-items-center rounded-lg border border-[var(--line)] bg-[var(--raised)] text-[var(--muted)] hover:text-[var(--text)] transition cursor-pointer" data-target="reason-d-{{ $i }}" aria-label="Show reasoning">
                  <svg class="w-3 h-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 9l6 6 6-6"/></svg>
                </button>
              </div>
            </td>
          </tr>
          <tr id="reason-d-{{ $i }}" class="hidden bg-[var(--base)]/40 border-none">
            <td colspan="10" class="px-5 py-3 border-none">
                <p class="text-[12px] leading-relaxed text-[var(--muted)] max-w-3xl m-0"><span class="font-semibold text-[var(--text)]">Reasoning · </span>{{ $signal->note }}</p>
            </td>
          </tr>
          @empty
          <tr>
            <td colspan="10" class="px-5 py-6 text-center text-[var(--muted)] border-none">No signals found — run <code class="num text-[11px] px-1.5 py-0.5 rounded bg-[var(--base)] border border-[var(--line)]">php artisan forex:scan</code>.</td>
          </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>

  <!-- Mobile Cards -->
  <div class="md:hidden space-y-3">
    @foreach ($signals as $i => $signal)
    @php 
        $buy = $signal->direction === 'buy'; 
        $expired = optional($signal->expires_at)->isPast();
        $feedClass = $signal->data_status === 'live' ? 'text-[var(--up)] border-[var(--up)]/25 bg-[var(--up)]/10' : 'text-[var(--warn)] border-[var(--warn)]/25 bg-[var(--warn)]/10';
        $feedLabel = strtoupper($signal->data_status ?? 'unknown') . (($signal->data_source ?? '') === 'yahoo' ? '' : ' · ' . strtoupper($signal->data_source ?? 'unknown'));
    @endphp
    <article class="rounded-xl border border-[var(--line)] bg-[var(--surface)] p-4 {{ $expired ? 'row-expired' : '' }}">
      <div class="flex items-start justify-between gap-3 mb-3">
        <div>
          <div class="flex items-center gap-2 m-0">
            <span class="font-semibold text-[14px]">{{ $signal->market->symbol }}</span>
            <span class="num text-[10px] px-1.5 rounded bg-[var(--raised)] border border-[var(--line)] text-[var(--muted)]">{{ $signal->timeframe }}</span>
            @if($signal->is_primary) <span class="text-[11px] text-[var(--warn)]">★</span> @endif
          </div>
          <span class="text-[11px] px-2 py-0.5 mt-1.5 inline-block rounded border border-[var(--brand)]/25 bg-[var(--brand)]/10 text-[var(--brand)]">{{ $signal->strategy }}</span>
        </div>
        <span class="text-[11px] font-bold px-2 py-1 rounded border {{ $buy ? 'text-[var(--up)] border-[var(--up)]/25 bg-[var(--up)]/10' : 'text-[var(--down)] border-[var(--down)]/25 bg-[var(--down)]/10' }}">{{ strtoupper($signal->direction) }}</span>
      </div>
      <dl class="grid grid-cols-2 gap-px rounded-lg overflow-hidden bg-[var(--line)] border border-[var(--line)] text-[12px] mb-3 m-0">
        <div class="bg-[var(--surface)] px-3 py-2"><dt class="text-[10px] uppercase tracking-wider text-[var(--muted)] mb-0.5 m-0">Entry</dt><dd class="num font-semibold m-0">{{ number_format($signal->entry, $signal->market->precision()) }}</dd></div>
        <div class="bg-[var(--surface)] px-3 py-2"><dt class="text-[10px] uppercase tracking-wider text-[var(--muted)] mb-0.5 m-0">Stop loss</dt><dd class="num font-semibold text-[var(--down)] m-0">{{ number_format($signal->stop_loss, $signal->market->precision()) }}</dd></div>
        <div class="bg-[var(--surface)] px-3 py-2"><dt class="text-[10px] uppercase tracking-wider text-[var(--muted)] mb-0.5 m-0">Targets</dt><dd class="num text-[11px] text-[var(--up)] m-0">{{ number_format($signal->tp1, $signal->market->precision()) }} · {{ number_format($signal->tp2, $signal->market->precision()) }} · <span class="font-semibold">{{ number_format($signal->take_profit, $signal->market->precision()) }}</span></dd></div>
        <div class="bg-[var(--surface)] px-3 py-2"><dt class="text-[10px] uppercase tracking-wider text-[var(--muted)] mb-0.5 m-0">R:R</dt><dd class="num font-semibold m-0">{{ number_format($signal->risk_reward, 1) }}</dd></div>
      </dl>
      <div class="flex items-center gap-2 mb-3">
        <span class="w-full h-1 rounded-full bg-[var(--raised)] overflow-hidden"><span class="block h-full rounded-full {{ $buy ? 'bg-[var(--up)]' : 'bg-[var(--down)]' }}" style="width:{{ $signal->confidence }}%"></span></span>
        <span class="num text-[11px] font-semibold shrink-0">{{ $signal->confidence }}%</span>
      </div>
      <p class="text-[12px] leading-relaxed text-[var(--muted)] mb-3 m-0">{{ $signal->note }}</p>
      <div class="flex items-center justify-between gap-2 m-0">
        <div class="flex items-center gap-2">
          <span class="text-[9px] font-bold tracking-wider px-1.5 py-0.5 rounded border {{ $feedClass }}">{{ $feedLabel }}</span>
          <span class="text-[10px] text-[var(--muted)]">{{ $expired ? 'Expired' : (optional($signal->expires_at)?->diffForHumans() ?? '—') }}</span>
        </div>
        <button type="button" class="js-copy inline-flex items-center gap-1.5 h-8 px-3 rounded-lg border border-[var(--line)] bg-[var(--raised)] text-[11px] font-semibold hover:border-[var(--brand)] transition cursor-pointer" data-text="{{ $signal->direction === 'buy' ? 'BUY' : 'SELL' }} {{ $signal->market->symbol }} \nEntry: {{ number_format($signal->entry, $signal->market->precision()) }} \nSL: {{ number_format($signal->stop_loss, $signal->market->precision()) }} \nTP1: {{ number_format($signal->tp1, $signal->market->precision()) }} \nTP2: {{ number_format($signal->tp2, $signal->market->precision()) }} \nTP3: {{ number_format($signal->take_profit, $signal->market->precision()) }}">
          <svg class="w-3 h-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="9" y="9" width="11" height="11" rx="2"/><path d="M5 15V5a2 2 0 012-2h10"/></svg>
          Copy
        </button>
      </div>
    </article>
    @endforeach
  </div>

  <!-- Pagination -->
  @if($signals->hasPages())
  <div class="flex items-center justify-between gap-4 px-4 py-3 rounded-xl border border-[var(--line)] bg-[var(--surface)] text-[12px]">
    @if ($signals->onFirstPage())
        <span class="inline-flex items-center justify-center gap-2 px-3 py-1.5 rounded-lg bg-[var(--base)] text-[var(--muted)] cursor-not-allowed">
          <svg class="w-3 h-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M15 18l-6-6 6-6"/></svg>
          Previous
        </span>
    @else
        <a href="{{ $signals->previousPageUrl() }}" class="inline-flex items-center justify-center gap-2 px-3 py-1.5 rounded-lg bg-[var(--raised)] text-[var(--text)] no-underline hover:bg-[var(--line)] transition">
          <svg class="w-3 h-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M15 18l-6-6 6-6"/></svg>
          Previous
        </a>
    @endif
    
    <span class="text-[var(--muted)]">Page <span class="num font-semibold text-[var(--text)]">{{ $signals->currentPage() }}</span> of <span class="num">{{ $signals->lastPage() }}</span></span>
    
    @if ($signals->hasMorePages())
        <a href="{{ $signals->nextPageUrl() }}" class="inline-flex items-center justify-center gap-2 px-3 py-1.5 rounded-lg bg-[var(--raised)] text-[var(--text)] no-underline hover:bg-[var(--line)] transition">
          Next
          <svg class="w-3 h-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 18l6-6-6-6"/></svg>
        </a>
    @else
        <span class="inline-flex items-center justify-center gap-2 px-3 py-1.5 rounded-lg bg-[var(--base)] text-[var(--muted)] cursor-not-allowed">
          Next
          <svg class="w-3 h-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 18l6-6-6-6"/></svg>
        </span>
    @endif
  </div>
  @endif

</div>
@endsection

@push('scripts')
<script>
document.querySelectorAll('.js-copy').forEach(function (btn) {
  btn.addEventListener('click', function () {
    var text = btn.dataset.text;
    var done = function () {
      var original = btn.innerHTML;
      btn.innerHTML = '<svg class="w-3 h-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6L9 17l-5-5"/></svg> Copied';
      btn.style.color = 'var(--up)';
      btn.style.borderColor = 'var(--up)';
      setTimeout(function () { btn.innerHTML = original; btn.style.color = ''; btn.style.borderColor = ''; }, 1600);
    };
    if (navigator.clipboard && window.isSecureContext) { navigator.clipboard.writeText(text).then(done); }
    else {
      var ta = document.createElement('textarea');
      ta.value = text; ta.style.position = 'fixed'; ta.style.opacity = '0';
      document.body.appendChild(ta); ta.select();
      try { document.execCommand('copy'); done(); } catch (e) {}
      document.body.removeChild(ta);
    }
  });
});

document.querySelectorAll('.js-reason').forEach(function (btn) {
  btn.addEventListener('click', function () {
    var row = document.getElementById(btn.dataset.target);
    if (!row) return;
    row.classList.toggle('hidden');
    btn.classList.toggle('rotate-180');
  });
});
</script>
@endpush
