@extends('layouts.app')

@section('title', 'Markets')
@section('subtitle', 'All tracked instruments')

@section('content')
<div class="space-y-8">
    @foreach ($markets as $category => $group)
        <section>
            <div class="flex items-center gap-2.5 mb-4">
                <h2 class="text-[13px] font-semibold uppercase tracking-[0.14em] m-0">{{ ucfirst($category) }}</h2>
                <span class="num text-[11px] px-2 py-0.5 rounded-md bg-[var(--raised)] text-[var(--muted)] border border-[var(--line)]">{{ count($group) }}</span>
            </div>
            
            <div class="rounded-xl border border-[var(--line)] bg-[var(--surface)] overflow-hidden min-w-0">
                <div class="overflow-x-auto thin-scroll">
                    <table class="w-full text-[13px] min-w-[720px] m-0 border-collapse">
                        <thead>
                            <tr class="text-[10px] uppercase tracking-[0.12em] text-[var(--muted)] border-b border-[var(--line)] bg-[var(--base)]/40">
                                <th class="text-left font-medium px-5 py-3 border-none">Symbol</th>
                                <th class="text-left font-medium px-3 py-3 border-none">Name</th>
                                <th class="text-right font-medium px-3 py-3 border-none">Price</th>
                                <th class="text-right font-medium px-3 py-3 border-none">Change</th>
                                <th class="text-center font-medium px-3 py-3 border-none">AI Bias</th>
                                <th class="text-right font-medium px-5 py-3 border-none">Confidence</th>
                                <th class="text-right font-medium px-5 py-3 border-none">Signals</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[var(--line)]">
                            @foreach ($group as $market)
                            @php 
                                $up = $market->change_pct >= 0; 
                                $biasClass = $market->ai_bias === 'bullish' ? 'text-[var(--up)] bg-[var(--up)]/12 border-[var(--up)]/20'
                                           : ($market->ai_bias === 'bearish' ? 'text-[var(--down)] bg-[var(--down)]/12 border-[var(--down)]/20'
                                           : 'text-[var(--muted)] bg-[var(--raised)] border-[var(--line)]');
                            @endphp
                            <tr class="hover:bg-[var(--raised)]/50 transition-colors">
                                <td class="px-5 py-3.5 font-semibold border-none">
                                    <a href="{{ route('markets.show', $market) }}" class="text-inherit hover:text-[var(--brand)] no-underline">{{ $market->symbol }}</a>
                                </td>
                                <td class="px-3 py-3.5 text-[var(--muted)] border-none">{{ $market->name }}</td>
                                <td class="num px-3 py-3.5 text-right font-medium border-none">{{ number_format($market->price, $market->precision()) }}</td>
                                <td class="num px-3 py-3.5 text-right border-none {{ $up ? 'text-[var(--up)]' : 'text-[var(--down)]' }}">
                                    {{ $up ? '+' : '' }}{{ number_format($market->change_pct, 2) }}%
                                </td>
                                <td class="px-3 py-3.5 text-center border-none">
                                    <span class="text-[10px] px-1.5 py-0.5 rounded border capitalize {{ $biasClass }}">{{ $market->ai_bias ?? 'neutral' }}</span>
                                </td>
                                <td class="px-5 py-3.5 text-right border-none">
                                    <span class="inline-flex items-center gap-2">
                                        <span class="w-10 h-1 rounded-full bg-[var(--raised)] overflow-hidden">
                                            <span class="block h-full rounded-full bg-[var(--brand)]" style="width:{{ $market->ai_confidence }}%"></span>
                                        </span>
                                        <span class="num text-[11px] text-[var(--muted)] w-8 text-right">{{ $market->ai_confidence }}%</span>
                                    </span>
                                </td>
                                <td class="px-5 py-3.5 text-right text-[12px] text-[var(--muted)] border-none">
                                    {{ $market->active_signals_count }} active
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </section>
    @endforeach
</div>
@endsection
