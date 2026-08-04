@extends('layouts.app')

@section('title', 'Strategies')
@section('subtitle', 'Pluggable strategy engine')

@section('content')
<div class="space-y-8">
    
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-5">
        @foreach ($strategies as $strategy)
            <div class="rounded-xl border border-[var(--line)] bg-[var(--surface)] p-5 hover:border-[var(--brand)]/50 transition-colors group">
                <div class="flex items-start justify-between gap-4 mb-3">
                    <h3 class="text-[15px] font-bold text-[var(--text)] m-0 group-hover:text-[var(--brand)] transition-colors">{{ $strategy['name'] }}</h3>
                    <span class="text-[10px] font-bold tracking-widest px-2 py-0.5 rounded border border-[var(--brand)]/25 bg-[var(--brand)]/10 text-[var(--brand)]">{{ $strategy['code'] }}</span>
                </div>
                
                <p class="text-[13px] leading-relaxed text-[var(--text)]/70 m-0 mb-4 h-[60px] overflow-hidden text-ellipsis line-clamp-3">{{ $strategy['description'] }}</p>
                
                <div class="flex flex-wrap items-center gap-1.5 mb-5 h-[24px] overflow-hidden">
                    @foreach ($strategy['concepts'] as $concept)
                        <span class="text-[10px] px-1.5 py-0.5 rounded bg-[var(--base)] border border-[var(--line)] text-[var(--muted)] whitespace-nowrap">{{ $concept }}</span>
                    @endforeach
                </div>
                
                <div class="flex items-center gap-4 text-[12px] pt-4 border-t border-[var(--line)]">
                    <span class="text-[var(--text)]/80"><strong class="num text-[13px] text-[var(--text)] mr-1">{{ $strategy['active_signals'] }}</strong> active</span>
                    <span class="text-[var(--up)]/80"><strong class="num text-[13px] text-[var(--up)] mr-1">{{ $strategy['buy'] }}</strong> buy</span>
                    <span class="text-[var(--down)]/80"><strong class="num text-[13px] text-[var(--down)] mr-1">{{ $strategy['sell'] }}</strong> sell</span>
                </div>
            </div>
        @endforeach
    </div>

    <section>
        <div class="flex items-center gap-2.5 mb-4">
            <h2 class="text-[13px] font-semibold uppercase tracking-[0.14em] text-[var(--muted)] m-0">Add your own strategy</h2>
        </div>
        <div class="rounded-xl border border-[var(--line)] bg-[var(--surface)] p-5">
            <div class="flex items-start gap-3">
                <svg class="w-5 h-5 text-[var(--brand)] shrink-0 mt-0.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path><polyline points="7.5 4.21 12 6.81 16.5 4.21"></polyline><polyline points="7.5 19.79 7.5 14.6 3 12"></polyline><polyline points="21 12 16.5 14.6 16.5 19.79"></polyline><polyline points="3.27 6.96 12 12.01 20.73 6.96"></polyline><line x1="12" y1="22.08" x2="12" y2="12"></line></svg>
                <p class="text-[13px] leading-relaxed text-[var(--text)]/80 m-0">
                    Create a class in <code class="num text-[11px] px-1.5 py-0.5 rounded bg-[var(--base)] border border-[var(--line)] text-[var(--brand)]">app/Domain/Strategies</code> implementing <code class="num text-[11px] px-1.5 py-0.5 rounded bg-[var(--base)] border border-[var(--line)] text-[var(--brand)]">StrategyInterface</code>,
                    then register it in <code class="num text-[11px] px-1.5 py-0.5 rounded bg-[var(--base)] border border-[var(--line)] text-[var(--brand)]">StrategyRegistry::all()</code>. It will automatically appear here and be
                    picked up by the signal engine on the next <code class="num text-[11px] px-1.5 py-0.5 rounded bg-[var(--base)] border border-[var(--line)] text-[var(--brand)]">php artisan forex:scan</code>.
                </p>
            </div>
        </div>
    </section>

</div>
@endsection
