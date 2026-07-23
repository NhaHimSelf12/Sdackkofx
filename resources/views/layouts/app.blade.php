<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Dashboard') · {{ config('app.name', 'Sdach KOFX') }}</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link rel="stylesheet" href="{{ asset('css/v2.css') }}">
    <link rel="stylesheet" href="{{ asset('css/feed.css') }}">
    <link rel="stylesheet" href="{{ asset('css/signals.css') }}">
    <link rel="stylesheet" href="{{ asset('css/terminal.css') }}">
    <link rel="stylesheet" href="{{ asset('css/theme.css') }}">
    <link rel="stylesheet" href="{{ asset('css/ea.css') }}">
    <script src="{{ asset('js/theme.js') }}"></script>
</head>
<body>
<div class="mobile-header">
    <div class="brand"><span class="brand-mark">Sdach</span> KOFX</div>
    <button class="mobile-toggle" id="mobileToggle" aria-label="Toggle menu">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><line x1="3" y1="12" x2="21" y2="12"></line><line x1="3" y1="6" x2="21" y2="6"></line><line x1="3" y1="18" x2="21" y2="18"></line></svg>
    </button>
</div>
<div class="sidebar-overlay" id="sidebarOverlay"></div>
<aside class="sidebar">
    <div class="brand"><span class="brand-mark">Sdach</span> KOFX</div>
    <nav>
        <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">Dashboard</a>
        <a href="{{ route('terminal.show', \App\Models\Market::where('symbol','XAUUSD')->first() ?? \App\Models\Market::first()) }}" class="{{ request()->routeIs('terminal.*') ? 'active' : '' }}">Trading Terminal</a>
        <a href="{{ route('markets.index') }}" class="{{ request()->routeIs('markets.*') ? 'active' : '' }}">Markets</a>
        <a href="{{ route('signals.index') }}" class="{{ request()->routeIs('signals.*') ? 'active' : '' }}">Signals</a>
        <a href="{{ route('news.index') }}" class="{{ request()->routeIs('news.*') ? 'active' : '' }}">News</a>
        <a href="{{ route('strategies.index') }}" class="{{ request()->routeIs('strategies.*') ? 'active' : '' }}">Strategies</a>
        <a href="{{ route('lessons.index') }}" class="{{ request()->routeIs('lessons.*') ? 'active' : '' }}">Lessons</a>
        <div class="nav-divider"></div>
        <a href="{{ route('journal.index') }}" class="{{ request()->routeIs('journal.*') ? 'active' : '' }}">Trading Journal</a>
        <a href="{{ route('risk.index') }}" class="{{ request()->routeIs('risk.*') ? 'active' : '' }}">Risk Calculator</a>
        @if(auth()->user()?->isAdmin())
            <a href="{{ route('admin.index') }}" class="{{ request()->routeIs('admin.index') ? 'active' : '' }}">Users</a>
            <a href="{{ route('ea.index') }}" class="{{ request()->routeIs('ea.*') ? 'active' : '' }}">EA Bots</a>
            <a href="{{ route('admin.public-strategies.index') }}" class="{{ request()->routeIs('admin.public-strategies.*') ? 'active' : '' }}">Web Strategies</a>
        @endif
        <div class="nav-divider"></div>
        <a href="{{ route('home') }}" style="color: var(--blue);">← Back to Website</a>
    </nav>
    <div class="sidebar-footer">
        <div><span class="dot dot-green"></span>AI engine online</div>
        <button class="theme-toggle" type="button"><span class="theme-icon"></span><span class="theme-label">Light mode</span></button>
        @auth<div class="user-mini"><a href="{{ route('profile.edit') }}" style="display:flex;align-items:center;gap:10px;text-decoration:none;color:inherit;flex:1;">@if(auth()->user()->avatar)<img src="{{ str_starts_with(auth()->user()->avatar, 'http') ? auth()->user()->avatar : asset('storage/'.auth()->user()->avatar) }}" style="width:32px;height:32px;border-radius:50%;object-fit:cover;">@else<span>{{ strtoupper(substr(auth()->user()->name,0,1)) }}</span>@endif<div style="flex:1;overflow:hidden;"><strong>{{ auth()->user()->name }}</strong><small>{{ ucfirst(auth()->user()->role) }}</small></div></a><form method="POST" action="{{ route('logout') }}">@csrf<button class="logout-btn" title="Logout">↗</button></form></div>@endauth
    </div>
</aside>
<main class="main">
    <header class="topbar">
        <h1>@yield('title', 'Dashboard')</h1>
        <div class="topbar-meta">@yield('subtitle')</div>
    </header>
    @yield('content')
    <p class="disclaimer">Signals and analysis are generated for information and education only — not financial advice.</p>
</main>
<script src="{{ asset('js/app.js') }}" defer></script>
@stack('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const toggle = document.getElementById('mobileToggle');
        const sidebar = document.querySelector('.sidebar');
        const overlay = document.getElementById('sidebarOverlay');
        if (toggle && sidebar && overlay) {
            toggle.addEventListener('click', () => {
                sidebar.classList.add('open');
                overlay.classList.add('open');
            });
            overlay.addEventListener('click', () => {
                sidebar.classList.remove('open');
                overlay.classList.remove('open');
            });
        }
    });
</script>
</body>
</html>
