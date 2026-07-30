<!doctype html>
<html
  lang="en"
  class="light-style layout-menu-fixed layout-compact"
  data-assets-path="{{ asset('sneat') }}/"
  data-template="vertical-menu-template-free">
  <head>
    <meta charset="utf-8" />
    <meta
      name="viewport"
      content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />

    <title>@yield('title', 'Dashboard') - {{ config('app.name', 'Sdach KOFX') }}</title>

    <meta name="description" content="" />

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
      href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap"
      rel="stylesheet" />

    <link rel="stylesheet" href="{{ asset('sneat/vendor/fonts/iconify-icons.css') }}" />

    <!-- Core CSS -->
    <link rel="stylesheet" href="{{ asset('sneat/vendor/css/core.css') }}" class="template-customizer-core-css" />
    <link rel="stylesheet" href="{{ asset('sneat/vendor/css/theme-default.css') }}" class="template-customizer-theme-css" />
    <link rel="stylesheet" href="{{ asset('sneat/css/demo.css') }}" />
    <link rel="stylesheet" href="{{ asset('sneat/css/dark.css') }}" />

    <!-- Vendors CSS -->
    <link rel="stylesheet" href="{{ asset('sneat/vendor/libs/perfect-scrollbar/perfect-scrollbar.css') }}" />

    <!-- Helpers -->
    <script src="{{ asset('sneat/vendor/js/helpers.js') }}"></script>
    <script src="{{ asset('sneat/js/config.js') }}"></script>
    
    @stack('styles')
    
    <style>
      .feed-chip { font-size: 0.75rem; font-weight: 600; padding: 0.25rem 0.5rem; border-radius: 4px; display: inline-block; }
      .feed-live { background-color: rgba(40, 199, 111, 0.16); color: #28c76f; }
      .feed-delayed { background-color: rgba(255, 159, 67, 0.16); color: #ff9f43; }
      .feed-demo { background-color: rgba(234, 84, 85, 0.16); color: #ea5455; }
    </style>
  </head>

  <body>
    <!-- Layout wrapper -->
    <div class="layout-wrapper layout-content-navbar">
      <div class="layout-container">
        <!-- Menu -->
        <aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
          <div class="app-brand demo">
            <a href="{{ route('dashboard') }}" class="app-brand-link">
              <span class="app-brand-text demo menu-text fw-bold ms-2">Sdach KOFX</span>
            </a>

            <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto d-block d-xl-none">
              <i class="bx bx-chevron-left bx-sm align-middle"></i>
            </a>
          </div>

          <div class="menu-inner-shadow"></div>

          <ul class="menu-inner py-1">
            <li class="menu-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
              <a href="{{ route('dashboard') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-home-circle"></i>
                <div data-i18n="Analytics">Dashboard</div>
              </a>
            </li>
            <li class="menu-item {{ request()->routeIs('terminal.*') ? 'active' : '' }}">
              <a href="{{ route('terminal.show', \App\Models\Market::where('symbol','XAUUSD')->first() ?? \App\Models\Market::first()) }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-line-chart"></i>
                <div data-i18n="Terminal">Terminal</div>
              </a>
            </li>
            <li class="menu-item {{ request()->routeIs('markets.*') ? 'active' : '' }}">
              <a href="{{ route('markets.index') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-store-alt"></i>
                <div data-i18n="Markets">Markets</div>
              </a>
            </li>
            <li class="menu-item {{ request()->routeIs('signals.*') ? 'active' : '' }}">
              <a href="{{ route('signals.index') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-radar"></i>
                <div data-i18n="Signals">Signals</div>
              </a>
            </li>
            
            <li class="menu-header small text-uppercase">
              <span class="menu-header-text">Education & Tools</span>
            </li>
            <li class="menu-item {{ request()->routeIs('lessons.*') ? 'active' : '' }}">
              <a href="{{ route('lessons.index') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-book-open"></i>
                <div data-i18n="Lessons">Lessons</div>
              </a>
            </li>
            <li class="menu-item {{ request()->routeIs('strategies.*') ? 'active' : '' }}">
              <a href="{{ route('strategies.index') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-bulb"></i>
                <div data-i18n="Strategies">Strategies</div>
              </a>
            </li>
            <li class="menu-item {{ request()->routeIs('news.*') ? 'active' : '' }}">
              <a href="{{ route('news.index') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-news"></i>
                <div data-i18n="News">News</div>
              </a>
            </li>
            <li class="menu-item {{ request()->routeIs('journal.*') ? 'active' : '' }}">
              <a href="{{ route('journal.index') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-notepad"></i>
                <div data-i18n="Journal">Journal</div>
              </a>
            </li>
            <li class="menu-item {{ request()->routeIs('risk.*') ? 'active' : '' }}">
              <a href="{{ route('risk.index') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-calculator"></i>
                <div data-i18n="Risk">Risk Calculator</div>
              </a>
            </li>

            @if(auth()->user()?->isAdmin())
            <li class="menu-header small text-uppercase">
              <span class="menu-header-text">Admin</span>
            </li>
            <li class="menu-item {{ request()->routeIs('admin.index') ? 'active' : '' }}">
              <a href="{{ route('admin.index') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-group"></i>
                <div data-i18n="Users">Users</div>
              </a>
            </li>
            <li class="menu-item {{ request()->routeIs('ea.*') ? 'active' : '' }}">
              <a href="{{ route('ea.index') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-bot"></i>
                <div data-i18n="EA Bots">EA Bots</div>
              </a>
            </li>
            <li class="menu-item {{ request()->routeIs('admin.public-strategies.*') ? 'active' : '' }}">
              <a href="{{ route('admin.public-strategies.index') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-share-alt"></i>
                <div data-i18n="Web Strategies">Web Strategies</div>
              </a>
            </li>
            @endif
          </ul>
        </aside>
        <!-- / Menu -->

        <!-- Layout container -->
        <div class="layout-page">
          <!-- Navbar -->
          <nav
            class="layout-navbar container-xxl navbar navbar-expand-xl navbar-detached align-items-center bg-navbar-theme"
            id="layout-navbar">
            <div class="layout-menu-toggle navbar-nav align-items-xl-center me-3 me-xl-0 d-xl-none">
              <a class="nav-item nav-link px-0 me-xl-4" href="javascript:void(0)">
                <i class="bx bx-menu bx-sm"></i>
              </a>
            </div>

            <div class="navbar-nav-right d-flex align-items-center" id="navbar-collapse">
              <div class="navbar-nav align-items-center">
                <div class="nav-item d-flex align-items-center">
                  <h5 class="mb-0">@yield('title', 'Dashboard')</h5>
                </div>
              </div>

              <ul class="navbar-nav flex-row align-items-center ms-auto">
                <li class="nav-item lh-1 me-3">
                  <a href="{{ route('home') }}" class="btn btn-sm btn-outline-primary">Back to Website</a>
                </li>
                <!-- Theme Style Switcher -->
                <li class="nav-item me-3">
                  <a class="nav-link theme-toggle" href="javascript:void(0);" onclick="toggleTheme()">
                    <i class="bx bx-moon bx-sm" id="theme-icon"></i>
                  </a>
                </li>
                <!-- / Theme Style Switcher -->

                @auth
                <li class="nav-item navbar-dropdown dropdown-user dropdown">
                  <a class="nav-link dropdown-toggle hide-arrow" href="javascript:void(0);" data-bs-toggle="dropdown">
                    <div class="avatar avatar-online">
                      @if(auth()->user()->avatar)
                      <img src="{{ str_starts_with(auth()->user()->avatar, 'http') ? auth()->user()->avatar : asset('storage/'.auth()->user()->avatar) }}" class="w-px-40 h-auto rounded-circle" />
                      @else
                      <span class="avatar-initial rounded-circle bg-primary">{{ strtoupper(substr(auth()->user()->name,0,1)) }}</span>
                      @endif
                    </div>
                  </a>
                  <ul class="dropdown-menu dropdown-menu-end">
                    <li>
                      <a class="dropdown-item" href="{{ route('profile.edit') }}">
                        <div class="d-flex">
                          <div class="flex-shrink-0 me-3">
                            <div class="avatar avatar-online">
                              @if(auth()->user()->avatar)
                              <img src="{{ str_starts_with(auth()->user()->avatar, 'http') ? auth()->user()->avatar : asset('storage/'.auth()->user()->avatar) }}" class="w-px-40 h-auto rounded-circle" />
                              @else
                              <span class="avatar-initial rounded-circle bg-primary">{{ strtoupper(substr(auth()->user()->name,0,1)) }}</span>
                              @endif
                            </div>
                          </div>
                          <div class="flex-grow-1">
                            <span class="fw-semibold d-block">{{ auth()->user()->name }}</span>
                            <small class="text-muted">{{ ucfirst(auth()->user()->role) }}</small>
                          </div>
                        </div>
                      </a>
                    </li>
                    <li>
                      <div class="dropdown-divider"></div>
                    </li>
                    <li>
                      <a class="dropdown-item" href="{{ route('profile.edit') }}">
                        <i class="bx bx-user me-2"></i>
                        <span class="align-middle">My Profile</span>
                      </a>
                    </li>
                    <li>
                      <div class="dropdown-divider"></div>
                    </li>
                    <li>
                      <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <a class="dropdown-item" href="#" onclick="event.preventDefault(); this.closest('form').submit();">
                          <i class="bx bx-power-off me-2"></i>
                          <span class="align-middle">Log Out</span>
                        </a>
                      </form>
                    </li>
                  </ul>
                </li>
                @endauth
              </ul>
            </div>
          </nav>
          <!-- / Navbar -->

          <!-- Content wrapper -->
          <div class="content-wrapper">
            <!-- Content -->
            <div class="container-xxl flex-grow-1 container-p-y">
              @yield('content')
            </div>
            <!-- / Content -->

            <div class="content-backdrop fade"></div>
          </div>
          <!-- Content wrapper -->
        </div>
        <!-- / Layout page -->
      </div>
      <!-- Overlay -->
      <div class="layout-overlay layout-menu-toggle"></div>
    </div>
    <!-- / Layout wrapper -->

    <!-- Core JS -->
    <script src="{{ asset('sneat/vendor/libs/jquery/jquery.js') }}"></script>
    <script src="{{ asset('sneat/vendor/libs/popper/popper.js') }}"></script>
    <script src="{{ asset('sneat/vendor/js/bootstrap.js') }}"></script>
    <script src="{{ asset('sneat/vendor/libs/perfect-scrollbar/perfect-scrollbar.js') }}"></script>
    <script src="{{ asset('sneat/vendor/js/menu.js') }}"></script>

    <!-- Main JS -->
    <script src="{{ asset('sneat/js/main.js') }}"></script>

    @stack('scripts')
    <script>
      function toggleTheme() {
        const html = document.documentElement;
        if (html.classList.contains('dark-style')) {
          localStorage.setItem('theme', 'light');
        } else {
          localStorage.setItem('theme', 'dark');
        }
        window.location.reload(); // Reload to apply theme to charts
      }

      // Initialize theme on load
      (function() {
        const savedTheme = localStorage.getItem('theme');
        const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
        const isDark = savedTheme === 'dark' || (!savedTheme && prefersDark);
        
        const html = document.documentElement;
        const icon = document.getElementById('theme-icon');
        
        if (isDark) {
          html.classList.remove('light-style');
          html.classList.add('dark-style');
          if (icon) {
            icon.classList.remove('bx-moon');
            icon.classList.add('bx-sun');
          }
        }
      })();
    </script>
  </body>
</html>
