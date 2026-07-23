<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>មេរៀន (Lessons) · FX Command</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link rel="stylesheet" href="{{ asset('css/public.css') }}">
    <link rel="stylesheet" href="{{ asset('css/theme.css') }}">
    <script src="{{ asset('js/theme.js') }}"></script>
    <style>
        .lesson-page-container { max-width: 1200px; margin: 0 auto; padding: 120px 20px 60px; min-height: 100vh; }
    </style>
</head>
<body class="public-body">
    <header class="public-nav">
        <a class="public-brand" href="{{ route('home') }}">
            <span class="brand-mark">FX</span><strong>Command</strong>
        </a>
        <nav>
            <a href="{{ route('home') }}#markets">Markets</a>
            <a href="{{ route('home') }}#features">Platform</a>
            <a href="{{ route('home') }}#strategies">Strategy</a>
            <a href="{{ route('home') }}#news">News</a>
            <a href="{{ route('public.learn') }}" style="color: var(--blue);">Learn</a>
        </nav>
        <div class="nav-actions" style="margin-left: auto;">
            <button class="theme-toggle" type="button" aria-label="Toggle theme"><span class="theme-icon"></span></button>
            @auth
                <a class="btn" href="{{ route('dashboard') }}">Dashboard</a>
            @else
                <a class="nav-login" href="{{ route('login') }}">Log in</a>
                <a class="btn btn-primary" href="{{ route('register') }}">Start free</a>
            @endauth
        </div>
    </header>

    <main class="lesson-page-container">
        @include('partials.lesson_content')
    </main>

    <footer class="public-footer">
        <a class="public-brand"><span class="brand-mark">FX</span><strong>Command</strong></a>
        <p>Market analysis for education—not financial advice.</p>
        <span>© {{ date('Y') }} FX Command</span>
    </footer>

    <script src="{{ asset('js/public.js') }}" defer></script>
</body>
</html>
