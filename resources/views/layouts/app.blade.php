<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', config('app.name')) &mdash; Orderan Kokiku</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('head')
</head>
<body>

{{-- Overlay (mobile) --}}
<div class="kk-overlay" id="kkOverlay"></div>

<div class="kk-shell">

    {{-- ===== SIDEBAR ===== --}}
    <aside class="kk-sidebar" id="kkSidebar">
        {{-- Brand --}}
        <div class="kk-sidebar-brand">
            <div class="kk-sidebar-logo">
                <div class="kk-sidebar-logo-icon">
                    <i class="bi bi-fire"></i>
                </div>
                <div class="kk-sidebar-brand-text">
                    <div class="kk-sidebar-brand-name">Kokiku</div>
                    <div class="kk-sidebar-brand-sub">Inventory System</div>
                </div>
            </div>
            <button class="kk-sidebar-close" id="kkSidebarClose" aria-label="Tutup menu">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>

        {{-- Role badge --}}
        @auth
        <div style="padding: 0.75rem 1.25rem 0.25rem;">
            @if(auth()->user()->isAdmin())
                <span class="kk-role-badge admin"><i class="bi bi-shield-check"></i> Administrator</span>
            @elseif(auth()->user()->isKasir())
                <span class="kk-role-badge kasir"><i class="bi bi-cash-coin"></i> Kasir</span>
            @elseif(auth()->user()->isKitchen())
                <span class="kk-role-badge kitchen"><i class="bi bi-cup-hot"></i> Kitchen</span>
            @endif
        </div>
        @endauth

        {{-- Nav --}}
        <nav class="kk-sidebar-nav">
            @auth
                @if (auth()->user()->isAdmin())
                    @include('layouts.partials.nav-admin')
                @elseif (auth()->user()->isKasir())
                    @include('layouts.partials.nav-kasir')
                @elseif (auth()->user()->isKitchen())
                    @include('layouts.partials.nav-kitchen')
                @endif
            @endauth
        </nav>
    </aside>

    {{-- ===== MAIN ===== --}}
    <div class="kk-main">

        {{-- Topbar --}}
        <header class="kk-topbar">
            {{-- Left: Hamburger + Title --}}
            <div class="kk-topbar-left">
                <button class="kk-hamburger" id="kkHamburger" aria-label="Buka menu">
                    <i class="bi bi-list"></i>
                </button>
                <div class="kk-topbar-title d-flex align-items-center gap-2">
                    <h1>@yield('title', 'Dashboard')</h1>
                    @stack('topbar-extra')
                </div>
            </div>

            {{-- Right: Clock + Notif + User --}}
            <div class="kk-topbar-actions">
                {{-- Live Clock --}}
                <div class="kk-live-clock d-none d-md-flex" id="kkClock">
                    <i class="bi bi-clock" style="color:var(--kk-orange);"></i>
                    <span id="kkClockTime">--:--:--</span>
                </div>

                @auth
                    @include('layouts.partials.notifications-dropdown')

                    {{-- User Info --}}
                    <div class="d-flex align-items-center gap-2">
                        <div class="kk-user-avatar">
                            {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                        </div>
                        <div class="kk-user-info d-none d-sm-block">
                            <div class="kk-user-name-text">{{ auth()->user()->name }}</div>
                            <div class="kk-user-role-text">{{ auth()->user()->role?->name }}</div>
                        </div>
                    </div>

                    {{-- Logout --}}
                    <form method="POST" action="{{ route('logout') }}" class="mb-0">
                        @csrf
                        <button type="submit" class="btn btn-sm" style="background:var(--kk-danger-soft);color:var(--kk-danger);border:1px solid rgba(239,68,68,0.2);border-radius:var(--kk-radius-sm);padding:0.35rem 0.7rem;" title="Keluar">
                            <i class="bi bi-box-arrow-right"></i>
                            <span class="d-none d-lg-inline ms-1">Keluar</span>
                        </button>
                    </form>
                @endauth
            </div>
        </header>

        {{-- Content --}}
        <main class="kk-content">
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
                    <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
                    <i class="bi bi-exclamation-circle-fill me-2"></i> {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @yield('content')
        </main>
    </div>
</div>

<script>
(function () {
    // Sidebar toggle
    const sidebar   = document.getElementById('kkSidebar');
    const overlay   = document.getElementById('kkOverlay');
    const hamburger = document.getElementById('kkHamburger');
    const closeBtn  = document.getElementById('kkSidebarClose');

    function openSidebar()  { sidebar.classList.add('is-open'); overlay.classList.add('show'); document.body.style.overflow = 'hidden'; }
    function closeSidebar() { sidebar.classList.remove('is-open'); overlay.classList.remove('show'); document.body.style.overflow = ''; }

    hamburger.addEventListener('click', openSidebar);
    closeBtn.addEventListener('click', closeSidebar);
    overlay.addEventListener('click', closeSidebar);

    sidebar.querySelectorAll('.nav-link:not(.disabled)').forEach(function (link) {
        link.addEventListener('click', function () { if (window.innerWidth < 768) closeSidebar(); });
    });

    // Live Clock
    const clockEl = document.getElementById('kkClockTime');
    if (clockEl) {
        function updateClock() {
            const now = new Date();
            const h = String(now.getHours()).padStart(2,'0');
            const m = String(now.getMinutes()).padStart(2,'0');
            const s = String(now.getSeconds()).padStart(2,'0');
            clockEl.textContent = h + ':' + m + ':' + s;
        }
        updateClock();
        setInterval(updateClock, 1000);
    }
})();
</script>

@stack('scripts')
</body>
</html>
