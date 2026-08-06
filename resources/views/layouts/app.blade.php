<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', config('app.name')) &mdash; {{ config('app.name') }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>

{{-- Overlay (mobile) --}}
<div class="kk-overlay" id="kkOverlay"></div>

<div class="kk-shell">

    {{-- ===== SIDEBAR ===== --}}
    <aside class="kk-sidebar" id="kkSidebar">
        <div class="kk-sidebar-brand">
            <div class="kk-sidebar-brand-text">
                Orderan Kokiku
                <small>Inventory System</small>
            </div>
            {{-- Close button (mobile) --}}
            <button class="kk-sidebar-close" id="kkSidebarClose" aria-label="Tutup menu">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>

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
            {{-- Hamburger (mobile) --}}
            <button class="kk-hamburger" id="kkHamburger" aria-label="Buka menu">
                <i class="bi bi-list"></i>
            </button>

            <div class="kk-topbar-title">
                <h1 class="h6 mb-0 fw-semibold">@yield('title', 'Dashboard')</h1>
            </div>

            <div class="kk-topbar-actions">
                @auth
                    <div class="text-end kk-user-name">
                        <div class="fw-semibold" style="font-size: 0.88rem; line-height:1.2">{{ auth()->user()->name }}</div>
                        <div class="text-muted" style="font-size: 0.74rem;">{{ auth()->user()->role?->name }}</div>
                    </div>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="btn btn-outline-secondary btn-sm">
                            <i class="bi bi-box-arrow-right"></i>
                            <span class="d-none d-sm-inline">Keluar</span>
                        </button>
                    </form>
                @endauth
            </div>
        </header>

        {{-- Content --}}
        <main class="kk-content">
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show mb-3" role="alert">
                    <i class="bi bi-check-circle me-1"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show mb-3" role="alert">
                    <i class="bi bi-exclamation-circle me-1"></i> {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @yield('content')
        </main>
    </div>
</div>

{{-- Sidebar toggle script --}}
<script>
(function () {
    const sidebar  = document.getElementById('kkSidebar');
    const overlay  = document.getElementById('kkOverlay');
    const hamburger = document.getElementById('kkHamburger');
    const closeBtn  = document.getElementById('kkSidebarClose');

    function openSidebar() {
        sidebar.classList.add('is-open');
        overlay.classList.add('show');
        document.body.style.overflow = 'hidden';
    }

    function closeSidebar() {
        sidebar.classList.remove('is-open');
        overlay.classList.remove('show');
        document.body.style.overflow = '';
    }

    hamburger.addEventListener('click', openSidebar);
    closeBtn.addEventListener('click', closeSidebar);
    overlay.addEventListener('click', closeSidebar);

    // Close sidebar on nav-link click (mobile UX)
    sidebar.querySelectorAll('.nav-link:not(.disabled)').forEach(function (link) {
        link.addEventListener('click', function () {
            if (window.innerWidth < 768) closeSidebar();
        });
    });
})();
</script>

</body>
</html>
