@extends('layouts.app')

@section('title', 'News')
@section('subtitle', 'Analyzed financial headlines')

@section('content')
    <div class="chip-row">
        <a class="chip {{ !request('sentiment') && !request('impact') ? 'active' : '' }}" href="{{ route('news.index') }}">All</a>
        <a class="chip {{ request('sentiment') === 'bullish' ? 'active' : '' }}" href="{{ route('news.index', ['sentiment' => 'bullish']) }}">Bullish</a>
        <a class="chip {{ request('sentiment') === 'bearish' ? 'active' : '' }}" href="{{ route('news.index', ['sentiment' => 'bearish']) }}">Bearish</a>
        <a class="chip {{ request('sentiment') === 'neutral' ? 'active' : '' }}" href="{{ route('news.index', ['sentiment' => 'neutral']) }}">Neutral</a>
        <a class="chip {{ request('impact') === 'high' ? 'active' : '' }}" href="{{ route('news.index', ['impact' => 'high']) }}">High impact</a>
    </div>

    <div class="card">
        @forelse ($news as $item)
            <div class="news-item">
                <div class="news-title">
                    @if ($item->url)<a href="{{ $item->url }}" target="_blank" rel="noopener">{{ $item->title }}</a>@else{{ $item->title }}@endif
                </div>
                @if ($item->summary)<div class="news-summary">{{ $item->summary }}</div>@endif
                <div class="news-meta">
                    <span class="badge {{ $item->sentiment === 'bullish' ? 'badge-buy' : ($item->sentiment === 'bearish' ? 'badge-sell' : 'badge-neutral') }}">{{ ucfirst($item->sentiment) }}</span>
                    @if ($item->impact === 'high')
                        <span class="badge badge-orange">High impact</span>
                    @endif
                    @foreach ($item->symbols ?? [] as $symbol)
                        <span class="tag">{{ $symbol }}</span>
                    @endforeach
                    <span>{{ $item->source }}</span>
                    <span>· {{ optional($item->published_at)?->diffForHumans() }}</span>
                </div>
            </div>
        @empty
            <p class="muted">No news found.</p>
        @endforelse
    </div>

    <div class="pager">
        @if ($news->onFirstPage())
            <span class="disabled">← Previous</span>
        @else
            <a href="{{ $news->previousPageUrl() }}">← Previous</a>
        @endif
        <span>Page {{ $news->currentPage() }} of {{ $news->lastPage() }}</span>
        @if ($news->hasMorePages())
            <a href="{{ $news->nextPageUrl() }}">Next →</a>
        @else
            <span class="disabled">Next →</span>
        @endif
    </div>
@endsection
