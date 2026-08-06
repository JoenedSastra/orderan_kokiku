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
    <div class="kk-shell">
        <aside class="kk-sidebar">
            <div class="kk-sidebar-brand">
                Orderan Kokiku
                <small>Inventory System</small>
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

        <div class="kk-main">
            <header class="kk-topbar">
                <div>
                    <h1 class="h5 mb-0">@yield('title', 'Dashboard')</h1>
                </div>

                <div class="d-flex align-items-center gap-3">
                    @auth
                        <div class="text-end d-none d-sm-block">
                            <div class="fw-semibold" style="font-size: 0.9rem;">{{ auth()->user()->name }}</div>
                            <div class="text-muted" style="font-size: 0.78rem;">{{ auth()->user()->role?->name }}</div>
                        </div>

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="btn btn-outline-secondary btn-sm">
                                <i class="bi bi-box-arrow-right"></i> Keluar
                            </button>
                        </form>
                    @endauth
                </div>
            </header>

            <main class="kk-content">
                @if (session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif

                @if (session('error'))
                    <div class="alert alert-danger">{{ session('error') }}</div>
                @endif

                @yield('content')
            </main>
        </div>
    </div>
</body>
</html>
