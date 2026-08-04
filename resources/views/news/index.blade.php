@extends('layouts.app')

@section('title', 'News')
@section('subtitle', 'Analyzed financial headlines')

@section('content')
<div class="space-y-6">

    <!-- Filters -->
    <div class="flex flex-wrap items-center gap-2">
        <a href="{{ route('news.index') }}" class="px-3 py-1.5 rounded-full text-[11px] font-medium border no-underline transition {{ !request('sentiment') && !request('impact') ? 'bg-[var(--text)] text-[var(--base)] border-[var(--text)]' : 'bg-[var(--surface)] text-[var(--text)] border-[var(--line)] hover:bg-[var(--raised)]' }}">All</a>
        <a href="{{ route('news.index', ['sentiment' => 'bullish']) }}" class="px-3 py-1.5 rounded-full text-[11px] font-medium border no-underline transition {{ request('sentiment') === 'bullish' ? 'bg-[var(--up)] text-white border-[var(--up)]' : 'bg-[var(--surface)] text-[var(--text)] border-[var(--line)] hover:bg-[var(--raised)]' }}">Bullish</a>
        <a href="{{ route('news.index', ['sentiment' => 'bearish']) }}" class="px-3 py-1.5 rounded-full text-[11px] font-medium border no-underline transition {{ request('sentiment') === 'bearish' ? 'bg-[var(--down)] text-white border-[var(--down)]' : 'bg-[var(--surface)] text-[var(--text)] border-[var(--line)] hover:bg-[var(--raised)]' }}">Bearish</a>
        <a href="{{ route('news.index', ['sentiment' => 'neutral']) }}" class="px-3 py-1.5 rounded-full text-[11px] font-medium border no-underline transition {{ request('sentiment') === 'neutral' ? 'bg-[var(--raised)] text-[var(--text)] border-[var(--line)]' : 'bg-[var(--surface)] text-[var(--text)] border-[var(--line)] hover:bg-[var(--raised)]' }}">Neutral</a>
        <a href="{{ route('news.index', ['impact' => 'high']) }}" class="px-3 py-1.5 rounded-full text-[11px] font-medium border no-underline transition {{ request('impact') === 'high' ? 'bg-[var(--warn)] text-white border-[var(--warn)]' : 'bg-[var(--surface)] text-[var(--text)] border-[var(--line)] hover:bg-[var(--raised)]' }}">High impact</a>
    </div>

    <!-- News List -->
    <div class="rounded-xl border border-[var(--line)] bg-[var(--surface)] min-w-0">
        <div class="divide-y divide-[var(--line)]">
            @forelse ($news as $item)
            @php
                $biasClass = $item->sentiment === 'bullish' ? 'text-[var(--up)] bg-[var(--up)]/12 border-[var(--up)]/20'
                           : ($item->sentiment === 'bearish' ? 'text-[var(--down)] bg-[var(--down)]/12 border-[var(--down)]/20'
                           : 'text-[var(--muted)] bg-[var(--raised)] border-[var(--line)]');
            @endphp
            <article class="px-5 py-5 hover:bg-[var(--raised)]/40 transition-colors">
                <h3 class="text-[14px] font-semibold leading-snug mb-1 m-0">
                    @if ($item->url)
                        <a href="{{ $item->url }}" target="_blank" rel="noopener" class="text-inherit hover:text-[var(--brand)] transition-colors no-underline">{{ $item->title }}</a>
                    @else
                        {{ $item->title }}
                    @endif
                </h3>
                @if ($item->summary)
                    <p class="text-[13px] leading-relaxed text-[var(--text)]/80 mt-1.5 mb-3">{{ $item->summary }}</p>
                @else
                    <div class="mb-3"></div>
                @endif
                <div class="flex flex-wrap items-center gap-2 text-[11px] text-[var(--muted)]">
                    <span class="px-2 py-0.5 rounded border capitalize {{ $biasClass }}">{{ $item->sentiment }}</span>
                    @if ($item->impact === 'high')<span class="px-2 py-0.5 rounded border border-[var(--warn)]/25 bg-[var(--warn)]/10 text-[var(--warn)]">High impact</span>@endif
                    
                    @foreach ($item->symbols ?? [] as $symbol)
                        <span class="px-1.5 py-0.5 rounded border border-[var(--line)] bg-[var(--base)] font-medium">{{ $symbol }}</span>
                    @endforeach

                    <span class="text-[var(--text)]/70 ml-1">{{ $item->source }}</span>
                    <span>· {{ optional($item->published_at)?->diffForHumans() }}</span>
                </div>
            </article>
            @empty
            <p class="px-5 py-6 text-center text-[var(--muted)] m-0">No news found.</p>
            @endforelse
        </div>
    </div>

    <!-- Pagination -->
    <div class="flex items-center justify-between gap-4 px-4 py-3 rounded-xl border border-[var(--line)] bg-[var(--surface)] text-[12px]">
        @if ($news->onFirstPage())
            <span class="text-[var(--muted)] px-3 py-1.5 rounded-lg bg-[var(--base)] cursor-not-allowed">← Previous</span>
        @else
            <a href="{{ $news->previousPageUrl() }}" class="text-[var(--text)] px-3 py-1.5 rounded-lg bg-[var(--raised)] hover:bg-[var(--line)] transition no-underline">← Previous</a>
        @endif
        <span class="font-medium text-[var(--muted)]">Page {{ $news->currentPage() }} of {{ $news->lastPage() }}</span>
        @if ($news->hasMorePages())
            <a href="{{ $news->nextPageUrl() }}" class="text-[var(--text)] px-3 py-1.5 rounded-lg bg-[var(--raised)] hover:bg-[var(--line)] transition no-underline">Next →</a>
        @else
            <span class="text-[var(--muted)] px-3 py-1.5 rounded-lg bg-[var(--base)] cursor-not-allowed">Next →</span>
        @endif
    </div>

</div>
@endsection
