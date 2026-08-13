<!DOCTYPE html>
<html lang="en" class="h-full">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>@yield('title', 'Dashboard') · {{ config('app.name', 'Sdach KOFX') }}</title>

  <!-- Only inject specific css if needed by child pages -->
  @stack('styles')

  <script src="{{ asset('js/theme.js') }}"></script>

  <!-- Tailwind & Google Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link
    href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500;700&family=Battambang:wght@400;700&display=swap"
    rel="stylesheet">
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = {
      theme: {
        extend: {
          fontFamily: {
            sans: ['Inter', 'Battambang', 'system-ui', 'sans-serif'],
            mono: ['JetBrains Mono', 'Menlo', 'monospace']
          }
        }
      }
    }
  </script>
  <script>
      (function () {
        var t = localStorage.getItem('kofx-theme');
        if (!t) t = window.matchMedia('(prefers-color-scheme: light)').matches ? 'light' : 'dark';
        document.documentElement.classList.toggle('light', t === 'light');
      })();
  </script>
  <style>
    :root {
      --base: #0A0D14;
      --surface: #111621;
      --raised: #1A2130;
      --line: #242C3D;
      --text: #FFFFFF;
      --muted: #8B94A7;
      --brand: #5E9FE8;
      --up: #3DD68C;
      --down: #F0616D;
      --warn: #E5A155;
    }

    html.light {
      --base: #F7F8FA;
      --surface: #FFFFFF;
      --raised: #F0F2F6;
      --line: #E4E7EE;
      --text: #141821;
      --muted: #6B7385;
      --brand: #2783DE;
      --up: #16A265;
      --down: #D9414F;
      --warn: #C57A22;
    }

    body {
      font-family: 'Inter', system-ui, sans-serif;
      -webkit-font-smoothing: antialiased;
    }

    .num {
      font-family: 'JetBrains Mono', Menlo, monospace;
      font-variant-numeric: tabular-nums;
    }

    .nav-link.active {
      background: color-mix(in srgb, var(--brand) 12%, transparent);
      color: var(--text);
    }

    .nav-link.active::before {
      content: '';
      position: absolute;
      left: 0;
      top: 50%;
      transform: translateY(-50%);
      width: 3px;
      height: 18px;
      border-radius: 0 3px 3px 0;
      background: var(--brand);
    }

    .nav-link.active .nav-icon {
      color: var(--brand);
    }

    .pulse::before {
      content: '';
      position: absolute;
      inset: -4px;
      border-radius: 9999px;
      background: var(--up);
      opacity: .35;
      animation: pulse 2s ease-out infinite;
    }

    @keyframes pulse {
      0% {
        transform: scale(.6);
        opacity: .5
      }

      100% {
        transform: scale(1.6);
        opacity: 0
      }
    }

    .ticker-track {
      display: flex;
      width: max-content;
      animation: ticker 45s linear infinite;
    }

    .ticker:hover .ticker-track {
      animation-play-state: paused;
    }

    @keyframes ticker {
      to {
        transform: translateX(-50%);
      }
    }

    .thin-scroll::-webkit-scrollbar {
      width: 6px;
      height: 6px;
    }

    .thin-scroll::-webkit-scrollbar-thumb {
      background: var(--line);
      border-radius: 99px;
    }

    .thin-scroll::-webkit-scrollbar-track {
      background: transparent;
    }

    @media (prefers-reduced-motion: reduce) {

      *,
      *::before,
      *::after {
        animation: none !important;
        transition: none !important;
      }
    }

    /* Fix layout conflict between app.css and tailwind */
    .sidebar {
      display: none !important;
    }

    #sidebar {
      display: flex !important;
    }

    .main {
      margin-left: 0 !important;
      padding: 0 !important;
      width: 100% !important;
    }

    .mobile-header {
      display: none !important;
    }

    #sidebarOverlay {
      display: block !important;
    }
  </style>
</head>

<body class="h-full bg-[var(--base)] text-[var(--text)] antialiased">

  <!-- ================= MOBILE HEADER ================= -->
  <div
    class="lg:hidden fixed top-0 left-0 w-full z-50 h-14 flex items-center justify-between px-4 border-b border-[var(--line)] bg-[var(--base)]/90 backdrop-blur-xl">
    <a href="{{ route('home') }}" class="flex items-center gap-2" style="text-decoration:none;">
      <span
        class="w-7 h-7 rounded-lg bg-[var(--brand)]/15 text-[var(--brand)] grid place-items-center text-[11px] font-bold">SK</span>
      <span class="font-semibold tracking-tight text-[15px] no-underline">Sdach <span
          class="text-[var(--muted)]">KOFX</span></span>
    </a>
    <button id="mobileToggle" aria-label="Toggle menu" aria-expanded="false"
      class="w-11 h-11 -mr-2 grid place-items-center rounded-lg text-[var(--muted)] hover:text-[var(--text)] hover:bg-[var(--raised)] transition bg-transparent border-none cursor-pointer">
      <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
        stroke-linecap="round">
        <line x1="3" y1="6" x2="21" y2="6" />
        <line x1="3" y1="12" x2="21" y2="12" />
        <line x1="3" y1="18" x2="21" y2="18" />
      </svg>
    </button>
  </div>

  <div id="sidebarOverlay"
    class="fixed inset-0 z-50 bg-black/60 backdrop-blur-sm opacity-0 pointer-events-none transition-opacity duration-300 lg:hidden">
  </div>

  <!-- ================= SIDEBAR ================= -->
  <aside id="sidebar" class="fixed inset-y-0 left-0 z-[60] w-[264px] flex flex-col border-r border-[var(--line)] bg-[var(--surface)]
              -translate-x-full lg:translate-x-0 transition-transform duration-300 ease-out">

    <div class="h-16 shrink-0 flex items-center gap-2.5 px-5 border-b border-[var(--line)]">
      <span
        class="w-8 h-8 rounded-lg bg-[var(--brand)]/15 text-[var(--brand)] grid place-items-center text-xs font-bold">SK</span>
      <span class="font-semibold tracking-tight">Sdach <span class="text-[var(--muted)]">KOFX</span></span>
      <button id="sidebarClose" aria-label="Close menu"
        class="lg:hidden ml-auto w-9 h-9 grid place-items-center rounded-lg text-[var(--muted)] hover:text-[var(--text)] hover:bg-[var(--raised)] transition bg-transparent border-none cursor-pointer">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
          stroke-linecap="round">
          <line x1="18" y1="6" x2="6" y2="18" />
          <line x1="6" y1="6" x2="18" y2="18" />
        </svg>
      </button>
    </div>

    <nav class="flex-1 overflow-y-auto thin-scroll p-3 text-[13px] no-underline">
      <p class="px-3 pt-2 pb-2 text-[10px] font-semibold uppercase tracking-[0.16em] text-[var(--muted)]/70 m-0">Trading
      </p>

      <a href="{{ route('dashboard') }}"
        class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }} font-medium relative flex items-center gap-3 px-3 py-2.5 rounded-lg text-[var(--muted)] hover:text-[var(--text)] hover:bg-[var(--raised)] transition no-underline"
        style="text-decoration:none;">
        <svg class="nav-icon w-4 h-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"
          stroke-linecap="round" stroke-linejoin="round">
          <rect x="3" y="3" width="7" height="9" rx="1.5" />
          <rect x="14" y="3" width="7" height="5" rx="1.5" />
          <rect x="14" y="12" width="7" height="9" rx="1.5" />
          <rect x="3" y="16" width="7" height="5" rx="1.5" />
        </svg>
        Dashboard
      </a>

      <a href="{{ route('terminal.show', \App\Models\Market::where('symbol', 'XAUUSD')->first() ?? \App\Models\Market::first()) }}"
        class="nav-link {{ request()->routeIs('terminal.*') ? 'active' : '' }} relative flex items-center gap-3 px-3 py-2.5 rounded-lg text-[var(--muted)] hover:text-[var(--text)] hover:bg-[var(--raised)] transition no-underline"
        style="text-decoration:none;">
        <svg class="nav-icon w-4 h-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"
          stroke-linecap="round" stroke-linejoin="round">
          <path d="M3 17l5-6 4 4 5-8 4 5" />
          <path d="M3 21h18" />
        </svg>
        Trading Terminal
      </a>

      <a href="{{ route('markets.index') }}"
        class="nav-link {{ request()->routeIs('markets.*') ? 'active' : '' }} relative flex items-center gap-3 px-3 py-2.5 rounded-lg text-[var(--muted)] hover:text-[var(--text)] hover:bg-[var(--raised)] transition no-underline"
        style="text-decoration:none;">
        <svg class="nav-icon w-4 h-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"
          stroke-linecap="round">
          <circle cx="12" cy="12" r="9" />
          <path d="M3 12h18M12 3a15 15 0 010 18a15 15 0 010-18" />
        </svg>
        Markets
      </a>

      <a href="{{ route('signals.index') }}"
        class="nav-link {{ request()->routeIs('signals.*') ? 'active' : '' }} relative flex items-center gap-3 px-3 py-2.5 rounded-lg text-[var(--muted)] hover:text-[var(--text)] hover:bg-[var(--raised)] transition no-underline"
        style="text-decoration:none;">
        <svg class="nav-icon w-4 h-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"
          stroke-linecap="round" stroke-linejoin="round">
          <path d="M13 2L4.5 13.5H11l-1 8.5 8.5-11.5H12l1-8.5z" />
        </svg>
        Signals
      </a>

      <a href="{{ route('news.index') }}"
        class="nav-link {{ request()->routeIs('news.*') ? 'active' : '' }} relative flex items-center gap-3 px-3 py-2.5 rounded-lg text-[var(--muted)] hover:text-[var(--text)] hover:bg-[var(--raised)] transition no-underline"
        style="text-decoration:none;">
        <svg class="nav-icon w-4 h-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"
          stroke-linecap="round" stroke-linejoin="round">
          <rect x="3" y="5" width="18" height="14" rx="2" />
          <path d="M7 9h6M7 13h10M7 16h5" />
        </svg>
        News
      </a>

      <a href="{{ route('strategies.index') }}"
        class="nav-link {{ request()->routeIs('strategies.*') ? 'active' : '' }} relative flex items-center gap-3 px-3 py-2.5 rounded-lg text-[var(--muted)] hover:text-[var(--text)] hover:bg-[var(--raised)] transition no-underline"
        style="text-decoration:none;">
        <svg class="nav-icon w-4 h-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"
          stroke-linecap="round">
          <path d="M4 6h16M4 12h10M4 18h7" />
          <circle cx="18" cy="12" r="2" />
          <circle cx="15" cy="18" r="2" />
        </svg>
        Strategies
      </a>

      <a href="{{ route('lessons.index') }}"
        class="nav-link {{ request()->routeIs('lessons.*') ? 'active' : '' }} relative flex items-center gap-3 px-3 py-2.5 rounded-lg text-[var(--muted)] hover:text-[var(--text)] hover:bg-[var(--raised)] transition no-underline"
        style="text-decoration:none;">
        <svg class="nav-icon w-4 h-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"
          stroke-linecap="round" stroke-linejoin="round">
          <path d="M3 6.5A2.5 2.5 0 015.5 4H12v16H5.5A2.5 2.5 0 013 17.5v-11z" />
          <path d="M21 6.5A2.5 2.5 0 0018.5 4H12v16h6.5a2.5 2.5 0 002.5-2.5v-11z" />
        </svg>
        Lessons
      </a>

      <p class="px-3 pt-5 pb-2 text-[10px] font-semibold uppercase tracking-[0.16em] text-[var(--muted)]/70 m-0">Tools
      </p>

      <a href="{{ route('journal.index') }}"
        class="nav-link {{ request()->routeIs('journal.*') ? 'active' : '' }} relative flex items-center gap-3 px-3 py-2.5 rounded-lg text-[var(--muted)] hover:text-[var(--text)] hover:bg-[var(--raised)] transition no-underline"
        style="text-decoration:none;">
        <svg class="nav-icon w-4 h-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"
          stroke-linecap="round" stroke-linejoin="round">
          <path d="M6 3h11a2 2 0 012 2v14a2 2 0 01-2 2H6z" />
          <path d="M6 3v18M10 8h5M10 12h5" />
        </svg>
        Trading Journal
      </a>

      <a href="{{ route('risk.index') }}"
        class="nav-link {{ request()->routeIs('risk.*') ? 'active' : '' }} relative flex items-center gap-3 px-3 py-2.5 rounded-lg text-[var(--muted)] hover:text-[var(--text)] hover:bg-[var(--raised)] transition no-underline"
        style="text-decoration:none;">
        <svg class="nav-icon w-4 h-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"
          stroke-linecap="round" stroke-linejoin="round">
          <rect x="4" y="3" width="16" height="18" rx="2" />
          <path d="M8 7h8M8 12h.01M12 12h.01M16 12h.01M8 16h.01M12 16h.01M16 16h.01" />
        </svg>
        Risk Calculator
      </a>

      @if(auth()->user()?->isAdmin())
        <p class="px-3 pt-5 pb-2 text-[10px] font-semibold uppercase tracking-[0.16em] text-[var(--muted)]/70 m-0">Admin
        </p>

        <a href="{{ route('admin.index') }}"
          class="nav-link {{ request()->routeIs('admin.index') ? 'active' : '' }} relative flex items-center gap-3 px-3 py-2.5 rounded-lg text-[var(--muted)] hover:text-[var(--text)] hover:bg-[var(--raised)] transition no-underline"
          style="text-decoration:none;">
          <svg class="nav-icon w-4 h-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"
            stroke-linecap="round" stroke-linejoin="round">
            <circle cx="9" cy="8" r="3.2" />
            <path d="M3 20a6 6 0 0112 0" />
            <path d="M16 11a3 3 0 100-6" />
            <path d="M18 20a5 5 0 00-2-4" />
          </svg>
          Users
        </a>

        <a href="{{ route('ea.index') }}"
          class="nav-link {{ request()->routeIs('ea.*') ? 'active' : '' }} relative flex items-center gap-3 px-3 py-2.5 rounded-lg text-[var(--muted)] hover:text-[var(--text)] hover:bg-[var(--raised)] transition no-underline"
          style="text-decoration:none;">
          <svg class="nav-icon w-4 h-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"
            stroke-linecap="round" stroke-linejoin="round">
            <rect x="4" y="7" width="16" height="12" rx="3" />
            <path d="M12 7V4M9 13h.01M15 13h.01" />
          </svg>
          EA Bots
        </a>

        <a href="{{ route('admin.public-strategies.index') }}"
          class="nav-link {{ request()->routeIs('admin.public-strategies.*') ? 'active' : '' }} relative flex items-center gap-3 px-3 py-2.5 rounded-lg text-[var(--muted)] hover:text-[var(--text)] hover:bg-[var(--raised)] transition no-underline"
          style="text-decoration:none;">
          <svg class="nav-icon w-4 h-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"
            stroke-linecap="round" stroke-linejoin="round">
            <rect x="3" y="4" width="18" height="16" rx="2" />
            <path d="M3 9h18M8 4v5" />
          </svg>
          Web Strategies
        </a>
      @endif

      <div class="mt-6 mb-2">
        <!-- Telegram Community Button -->
        <a href="https://t.me/+74M0KgMxstFkYjY1" target="_blank"
          class="flex items-center gap-3 px-2 py-2 rounded-[24px] border border-[#29A9EA]/30 bg-[#090F1C] hover:bg-[#111A2C] hover:border-[#29A9EA]/60 transition-all duration-300 relative overflow-hidden group mx-1 no-underline"
          style="box-shadow: inset -3px -6px 15px rgba(41, 169, 234, 0.12);">
          <!-- Subtle glow inside -->
          <div
            class="absolute -right-3 -bottom-3 w-16 h-16 bg-[#29A9EA]/20 blur-xl rounded-full group-hover:bg-[#29A9EA]/30 transition-all duration-300">
          </div>

          <!-- Telegram Icon -->
          <div
            class="w-9 h-9 rounded-full bg-[#3390EC] flex items-center justify-center shrink-0 z-10 shadow-lg shadow-[#3390EC]/20">
            <svg class="w-[18px] h-[18px] text-white ml-[-1px] mt-[1px]" viewBox="0 0 24 24" fill="currentColor">
              <path
                d="M11.944 0A12 12 0 0 0 0 12a12 12 0 0 0 12 12 12 12 0 0 0 12-12A12 12 0 0 0 12 0a12 12 0 0 0-.056 0zm4.962 7.224c.1-.002.321.023.465.14a.506.506 0 0 1 .171.325c.016.093.036.306.02.472-.18 1.898-.962 6.502-1.36 8.627-.168.9-.499 1.201-.82 1.23-.696.065-1.225-.46-1.9-.902-1.056-.693-1.653-1.123-2.678-1.799-1.185-.78-.415-1.21.258-1.91.176-.184 3.247-2.977 3.307-3.23.007-.032.014-.15-.056-.212s-.174-.041-.249-.024c-.106.024-1.793 1.14-5.061 3.345-.48.33-.913.49-1.302.48-.428-.008-1.252-.241-1.865-.44-.752-.245-1.349-.374-1.297-.789.027-.216.325-.437.893-.664 3.498-1.524 5.83-2.529 6.998-3.014 3.332-1.386 4.025-1.627 4.476-1.635z" />
            </svg>
          </div>

          <div class="flex flex-col z-10 pr-2">
            <span class="text-[13px] font-bold text-white leading-tight font-sans tracking-wide">Join community</span>
            <span class="text-[10px] font-bold text-[#3390EC] tracking-wider uppercase mt-[1px]">Telegram · Free</span>
          </div>
        </a>
      </div>

      <div class="my-4 border-t border-[var(--line)]"></div>

      <a href="{{ route('home') }}"
        class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-[var(--brand)] hover:bg-[var(--raised)] transition no-underline"
        style="text-decoration:none;">
        <svg class="w-4 h-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"
          stroke-linecap="round" stroke-linejoin="round">
          <path d="M19 12H5M11 18l-6-6 6-6" />
        </svg>
        Back to Website
      </a>

      <button type="button" onclick="document.getElementById('supportModal').style.display='flex'; return false;"
        class="w-full flex items-center gap-3 px-3 py-2.5 rounded-lg text-[var(--up)] hover:bg-[var(--raised)] transition text-left cursor-pointer border-none bg-transparent m-0">
        <svg class="w-4 h-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"
          stroke-linecap="round" stroke-linejoin="round">
          <path d="M4 9h13v5a5 5 0 01-5 5H9a5 5 0 01-5-5V9z" />
          <path d="M17 10h1.5a2.5 2.5 0 010 5H17" />
          <path d="M7 3v2M11 3v2" />
        </svg>
        Support me
      </button>
    </nav>

    <div class="shrink-0 border-t border-[var(--line)] p-3 space-y-2">
      <div class="flex items-center gap-2.5 px-3 py-2 rounded-lg bg-[var(--base)] border border-[var(--line)]">
        <span class="relative w-1.5 h-1.5 rounded-full bg-[var(--up)] pulse"></span>
        <span class="text-[11px] font-medium text-[var(--up)] m-0">AI engine online</span>
      </div>

      <button type="button" id="themeToggle"
        class="w-full flex items-center gap-3 px-3 py-2.5 rounded-lg text-[13px] text-[var(--muted)] hover:text-[var(--text)] hover:bg-[var(--raised)] transition border-none bg-transparent cursor-pointer m-0">
        <span id="themeIcon" class="w-4 h-4 shrink-0 grid place-items-center"></span>
        <span id="themeLabel">Light mode</span>
      </button>

      @auth
        <div class="flex items-center gap-2 p-2 rounded-lg bg-[var(--base)] border border-[var(--line)]">
          <a href="{{ route('profile.edit') }}"
            class="flex items-center gap-2.5 flex-1 min-w-0 group no-underline text-inherit"
            style="text-decoration:none; color:inherit;">
            @if(auth()->user()->avatar)
              <img
                src="{{ str_starts_with(auth()->user()->avatar, 'http') ? auth()->user()->avatar : asset('storage/' . auth()->user()->avatar) }}"
                class="w-8 h-8 rounded-full object-cover shrink-0 m-0">
            @else
              <span
                class="w-8 h-8 rounded-full bg-[var(--brand)]/15 text-[var(--brand)] grid place-items-center text-xs font-semibold shrink-0 m-0">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</span>
            @endif
            <span class="min-w-0 flex-1 m-0">
              <span
                class="block text-[12.5px] font-medium truncate group-hover:text-[var(--brand)] transition m-0">{{ auth()->user()->name }}</span>
              <span
                class="block text-[10.5px] text-[var(--muted)] truncate m-0">{{ ucfirst(auth()->user()->role) }}</span>
            </span>
          </a>
          <form method="POST" action="{{ route('logout') }}" class="m-0 flex">
            @csrf
            <button type="submit" title="Log out" aria-label="Log out"
              class="w-9 h-9 grid place-items-center rounded-lg text-[var(--muted)] hover:text-[var(--down)] hover:bg-[var(--down)]/10 transition border-none bg-transparent cursor-pointer m-0">
              <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"
                stroke-linecap="round" stroke-linejoin="round">
                <path d="M15 17l5-5-5-5M20 12H9M12 20H6a2 2 0 01-2-2V6a2 2 0 012-2h6" />
              </svg>
            </button>
          </form>
        </div>
      @endauth
    </div>
  </aside>

  <!-- ================= MAIN ================= -->
  <div class="lg:pl-[264px] min-h-screen flex flex-col pt-14 lg:pt-0">

    <header
      class="hidden lg:flex sticky top-0 z-40 h-16 shrink-0 items-center justify-between gap-4 px-8 border-b border-[var(--line)] bg-[var(--base)]/85 backdrop-blur-xl">
      <div class="min-w-0">
        <h1 class="text-[15px] font-semibold leading-tight truncate m-0">@yield('title', 'Dashboard')</h1>
        <p class="text-[11px] text-[var(--muted)] truncate m-0">@yield('subtitle', 'Forex Management System')</p>
      </div>
      <div class="flex items-center gap-2">
        <div
          class="hidden xl:flex items-center gap-2 h-10 px-3.5 rounded-lg border border-[var(--line)] bg-[var(--surface)]">
          <span class="relative w-1.5 h-1.5 rounded-full bg-[var(--up)] pulse"></span>
          <span class="text-[11px] text-[var(--muted)]">Live feed</span>
        </div>
        <button
          class="w-10 h-10 grid place-items-center rounded-lg border border-[var(--line)] bg-[var(--surface)] text-[var(--muted)] hover:text-[var(--text)] transition cursor-pointer border-none"
          aria-label="Notifications">
          <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"
            stroke-linecap="round" stroke-linejoin="round">
            <path d="M18 8a6 6 0 10-12 0c0 7-3 8-3 8h18s-3-1-3-8" />
            <path d="M13.7 21a2 2 0 01-3.4 0" />
          </svg>
        </button>
        <form method="POST" action="{{ route('signals.refresh') }}" class="m-0 flex">
          @csrf
          @if(isset($market))
            <input type="hidden" name="symbol" value="{{ $market->symbol }}">
          @endif
          <button type="submit"
            class="h-10 inline-flex items-center gap-2.5 px-4 rounded-lg bg-[var(--brand)] text-white text-[13px] font-semibold hover:opacity-90 transition border-none cursor-pointer">
            <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
              stroke-linecap="round" stroke-linejoin="round">
              <path d="M21 12a9 9 0 11-3-6.7" />
              <path d="M21 3v6h-6" />
            </svg>
            Refresh signals
          </button>
        </form>
      </div>
    </header>

    <main class="flex-1 px-5 lg:px-8 py-6">
      <div class="max-w-[1500px] mx-auto">

        <div class="lg:hidden mb-5">
          <h1 class="text-lg font-semibold leading-tight m-0">@yield('title', 'Dashboard')</h1>
          <p class="text-[11px] text-[var(--muted)] mt-0.5 m-0">@yield('subtitle', 'Forex Management System')</p>
        </div>

        <!-- Page Content -->
        @yield('content')

        <p
          class="mt-10 pt-6 border-t border-[var(--line)] text-[11px] leading-relaxed text-[var(--muted)]/80 text-center m-0">
          Signals and analysis are generated for information and education only — not financial advice.
        </p>
      </div>
    </main>
  </div>

  <!-- Scripts -->
  <script src="{{ asset('js/app.js') }}" defer></script>
  @stack('scripts')
  @include('partials.support_modal')

  <script>
    // ---------- Mobile drawer ----------
    (function () {
      const toggle = document.getElementById('mobileToggle');
      const close = document.getElementById('sidebarClose');
      const sidebar = document.getElementById('sidebar');
      const overlay = document.getElementById('sidebarOverlay');
      const open = () => { sidebar.classList.remove('-translate-x-full'); overlay.classList.remove('opacity-0', 'pointer-events-none'); toggle.setAttribute('aria-expanded', 'true'); document.body.style.overflow = 'hidden'; };
      const shut = () => { sidebar.classList.add('-translate-x-full'); overlay.classList.add('opacity-0', 'pointer-events-none'); toggle.setAttribute('aria-expanded', 'false'); document.body.style.overflow = ''; };
      toggle?.addEventListener('click', open);
      close?.addEventListener('click', shut);
      overlay?.addEventListener('click', shut);
      document.addEventListener('keydown', e => { if (e.key === 'Escape') shut(); });
    })();

    // ---------- Theme toggle ----------
    (function () {
      const btn = document.getElementById('themeToggle');
      const icon = document.getElementById('themeIcon');
      const label = document.getElementById('themeLabel');
      const sun = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"><circle cx="12" cy="12" r="4"/><path d="M12 2v2M12 20v2M2 12h2M20 12h2M4.9 4.9l1.4 1.4M17.7 17.7l1.4 1.4M19.1 4.9l-1.4 1.4M6.3 17.7l-1.4 1.4"/></svg>';
      const moon = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M21 12.8A9 9 0 1111.2 3a7 7 0 009.8 9.8z"/></svg>';
      const paint = () => {
        const light = document.documentElement.classList.contains('light');
        if (icon) icon.innerHTML = light ? moon : sun;
        if (label) label.textContent = light ? 'Dark mode' : 'Light mode';
      };
      paint();
      btn?.addEventListener('click', () => {
        const light = document.documentElement.classList.toggle('light');
        localStorage.setItem('kofx-theme', light ? 'light' : 'dark');
        paint();
      });
    })();
  </script>
</body>

</html>