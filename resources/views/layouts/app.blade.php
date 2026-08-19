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
@php
    $themeClass = 'theme-admin';
    if (auth()->check()) {
        if (auth()->user()->isKasir()) $themeClass = 'theme-kasir';
        if (auth()->user()->isKitchen()) $themeClass = 'theme-kitchen';
    }
@endphp
<body class="{{ $themeClass }}">

{{-- Overlay (mobile) --}}
<div class="kk-overlay" id="kkOverlay"></div>

<div class="kk-shell">

    {{-- ===== SIDEBAR ===== --}}
    <aside class="kk-sidebar" id="kkSidebar">
        {{-- Brand --}}
        <div class="kk-sidebar-brand">
            <div class="kk-sidebar-logo">
                <div class="kk-sidebar-logo-icon" style="background: none; box-shadow: none;">
                    <img src="{{ asset('images/logo.jpg') }}" alt="Kokiku" style="width: 100%; height: 100%; object-fit: cover; border-radius: 50%;">
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
                    @stack('topbar-extra')
                </div>
            </div>

            {{-- Right: Clock + Notif + User --}}
            <div class="kk-topbar-actions">
                {{-- Theme Toggle --}}
                <button type="button" class="btn btn-sm d-flex align-items-center justify-content-center" id="themeToggleBtn" style="border:none; background:transparent; font-size:1.15rem; color:#4b5563; padding:0.35rem 0.5rem; border-radius:50%;" aria-label="Toggle Theme">
                    <i class="bi bi-moon-fill" id="themeToggleIcon"></i>
                </button>

                {{-- Live Clock --}}
                <div class="kk-live-clock d-none d-md-flex" id="kkClock">
                    <i class="bi bi-clock" style="color:var(--kk-orange);"></i>
                    <span id="kkClockTime">--:--:--</span>
                </div>

                @auth
                    @include('layouts.partials.notifications-dropdown')

                    {{-- User Info & Profile Card --}}
                    <div class="dropdown">
                        <div class="d-flex align-items-center gap-2" data-bs-toggle="dropdown" aria-expanded="false" style="cursor: pointer;" title="Lihat Profil">
                            <div class="kk-user-avatar" style="overflow: hidden; padding: 0;">
                                @if(auth()->user()->avatar)
                                    <img src="{{ asset('storage/' . auth()->user()->avatar) }}" alt="Avatar" style="width:100%; height:100%; object-fit:cover; border-radius:50%;">
                                @else
                                    {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                                @endif
                            </div>
                            <div class="kk-user-info d-none d-sm-block">
                                <div class="kk-user-name-text">{{ auth()->user()->name }}</div>
                                <div class="kk-user-role-text">{{ auth()->user()->role?->name }}</div>
                            </div>
                        </div>

                        <ul class="dropdown-menu dropdown-menu-end shadow-sm mt-2 p-0" style="min-width: 260px; border: 1px solid var(--kk-border); background-color: var(--kk-surface); border-radius: var(--kk-radius); overflow: hidden;">
                            <li>
                                <div class="px-4 py-4 text-center border-bottom" style="border-color: var(--kk-border) !important;">
                                    <div class="kk-user-avatar mx-auto mb-3" style="width: 72px; height: 72px; overflow: hidden; padding: 0; font-size: 1.8rem;">
                                        @if(auth()->user()->avatar)
                                            <img src="{{ asset('storage/' . auth()->user()->avatar) }}" alt="Avatar" style="width:100%; height:100%; object-fit:cover; border-radius:50%;">
                                        @else
                                            {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                                        @endif
                                    </div>
                                    <div style="font-weight: 700; color: var(--kk-text); font-size: 1.15rem;">{{ auth()->user()->name }}</div>
                                    <div style="font-size: 0.85rem; color: var(--kk-text-light); margin-bottom: 12px;" class="badge bg-secondary bg-opacity-25 text-secondary">{{ auth()->user()->role?->name }}</div>
                                    
                                    <form id="avatarForm" action="{{ route('profile.avatar') }}" method="POST" enctype="multipart/form-data" class="d-none">
                                        @csrf
                                        <input type="file" name="avatar" id="avatarInput" accept="image/*" onchange="document.getElementById('avatarForm').submit();">
                                    </form>
                                    <label for="avatarInput" class="btn btn-sm w-100 mb-0 transition-all" style="background-color: var(--kk-surface-hover); color: var(--kk-text); border: 1px solid var(--kk-border); border-radius: var(--kk-radius-sm); cursor: pointer;">
                                        <i class="bi bi-camera me-1"></i> Ubah Foto Profil
                                    </label>
                                </div>
                            </li>
                        </ul>
                    </div>

                    {{-- Logout --}}
                    <form method="POST" action="{{ route('logout') }}" class="mb-0">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-danger d-flex align-items-center" style="border-radius:var(--kk-radius-sm);padding:0.35rem 0.75rem; font-weight:500;" title="Keluar">
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

<script>
    (function () {
        const themeBtn = document.getElementById('themeToggleBtn');
        const themeIcon = document.getElementById('themeToggleIcon');
        const htmlEl = document.documentElement;
        
        // Force dark theme as default for modern design
        const savedTheme = localStorage.getItem('kk_theme') || 'dark';
        
        function setTheme(theme) {
            htmlEl.setAttribute('data-bs-theme', theme);
            if (theme === 'dark') {
                themeIcon.classList.remove('bi-sun-fill');
                themeIcon.classList.add('bi-moon-fill');
                themeIcon.style.color = '#f8f9fa'; // warna terang agar terlihat di mode gelap
            } else {
                themeIcon.classList.remove('bi-moon-fill');
                themeIcon.classList.add('bi-sun-fill');
                themeIcon.style.color = '#f59e0b'; // warna amber/kuning gelap agar terlihat
            }
            localStorage.setItem('kk_theme', theme);
        }
        
        setTheme(savedTheme);

        if(themeBtn) {
            themeBtn.addEventListener('click', () => {
                const current = htmlEl.getAttribute('data-bs-theme');
                setTheme(current === 'dark' ? 'light' : 'dark');
            });
        }
    })();
</script>

@stack('scripts')

{{-- ===== PAGE STATE PERSISTENCE ===== --}}
{{-- Menyimpan scroll + isian search per URL ke sessionStorage.            --}}
{{-- Data ini hanya hidup selama tab browser terbuka (bukan localStorage). --}}
<script>
(function () {
    'use strict';

    // Kunci penyimpanan unik per URL penuh (path + query string).
    var PAGE_KEY = 'kk_ps_' + window.location.pathname + window.location.search;

    // Halaman form create/tambah: tidak perlu restore scroll (selalu mulai atas).
    var IS_FORM_PAGE = /\/(tambah|create|edit|buat)\b/i.test(window.location.pathname);

    // ---------------------------------------------------------------
    // Tombol "Kembali" eksplisit (link dengan ?dari=kembali):
    // saat diklik, hapus state yang tersimpan untuk halaman TUJUAN
    // agar halaman hub/index terbuka fresh (tidak restore ke posisi lama).
    // ---------------------------------------------------------------
    document.querySelectorAll('a[href*="dari=kembali"]').forEach(function (link) {
        link.addEventListener('click', function () {
            try {
                var url  = new URL(link.href);
                var dest = url.pathname + url.search;
                sessionStorage.removeItem('kk_ps_' + dest);
            } catch (e) {}
        });
    });

    // ---------------------------------------------------------------
    // SIMPAN state saat meninggalkan halaman (navigasi ke mana saja).
    // ---------------------------------------------------------------
    window.addEventListener('beforeunload', function () {
        var state = { scrollY: window.scrollY, inputs: {} };

        // Input search live-filter (class kk-search-nama-barang, name="kk_search")
        document.querySelectorAll('.kk-search-nama-barang').forEach(function (el) {
            state.inputs['kk_search'] = el.value;
        });

        // Input select di dalam form GET (filter lokasi). Date tidak disimpan karena backend sudah merender value dari URL.
        document.querySelectorAll('form:not([method="POST"]) select[name]').forEach(function (el) {
            state.inputs[el.name] = el.value;
        });

        try {
            sessionStorage.setItem(PAGE_KEY, JSON.stringify(state));
        } catch (e) { /* storage penuh, lewati saja */ }
    });

    // ---------------------------------------------------------------
    // RESTORE state saat halaman dimuat kembali.
    // Tidak berlaku untuk halaman form create/tambah.
    // ---------------------------------------------------------------
    if (IS_FORM_PAGE) return;

    var saved;
    try { saved = JSON.parse(sessionStorage.getItem(PAGE_KEY) || 'null'); }
    catch (e) { saved = null; }

    if (!saved) return;

    // Restore input search
    if (saved.inputs && saved.inputs['kk_search']) {
        var searchEl = document.querySelector('.kk-search-nama-barang');
        if (searchEl) {
            searchEl.value = saved.inputs['kk_search'];
            // Picu event input agar live-filter JS yang ada ikut aktif kembali.
            searchEl.dispatchEvent(new Event('input', { bubbles: true }));
        }
    }

    // Restore filter date/select (tidak ikut-campur submit form)
    if (saved.inputs) {
        Object.keys(saved.inputs).forEach(function (name) {
            if (name === 'kk_search') return; // sudah ditangani di atas
            var el = document.querySelector('form:not([method="POST"]) [name="' + name + '"]');
            if (el) el.value = saved.inputs[name];
        });
    }

    // Restore posisi scroll — setelah layout selesai dirender.
    if (saved.scrollY && saved.scrollY > 0) {
        // Coba via load event, dengan fallback setTimeout kecil.
        var restored = false;
        window.addEventListener('load', function () {
            if (!restored) { window.scrollTo({ top: saved.scrollY, behavior: 'instant' }); restored = true; }
        });
        setTimeout(function () {
            if (!restored && window.scrollY === 0) {
                window.scrollTo({ top: saved.scrollY, behavior: 'instant' });
                restored = true;
            }
        }, 100);
    }
})();
</script>

<script>
    // Menghilangkan notifikasi (alert) otomatis dalam 3 detik
    document.addEventListener("DOMContentLoaded", function() {
        setTimeout(function() {
            var alerts = document.querySelectorAll('.alert');
            alerts.forEach(function(alert) {
                alert.classList.remove('show');
                setTimeout(function() {
                    alert.remove();
                }, 150);
            });
        }, 3000);
    });
</script>
<!-- Modal Notifikasi (ditempatkan di luar topbar agar z-index tidak tertutup backdrop) -->
<div class="modal fade" id="notifMessageModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content" style="background-color: var(--kk-surface); color: var(--kk-text); border: 1px solid var(--kk-border);">
      <div class="modal-header border-bottom-0">
        <h5 class="modal-title" id="notifMessageTitle" style="font-weight: 600; font-size:1.1rem;"></h5>
      </div>
      <div class="modal-body pt-0">
        <p id="notifMessageContent" style="white-space: pre-wrap; font-size: 0.9rem; color: var(--kk-text-light); margin-bottom: 0;"></p>
      </div>
      <div class="modal-footer border-top-0 pt-0">
        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal" style="border-radius:var(--kk-radius-sm);">Tutup</button>
        <form id="notifMessageForm" method="POST" action="" class="mb-0">
            @csrf
            <button type="submit" class="btn btn-sm text-white" style="background-color: var(--kk-orange); border: none; border-radius:var(--kk-radius-sm);">Tandai Dibaca</button>
        </form>
      </div>
    </div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const notifButtons = document.querySelectorAll('.kk-notif-modal-btn');
    const notifTitle = document.getElementById('notifMessageTitle');
    const notifContent = document.getElementById('notifMessageContent');
    const notifForm = document.getElementById('notifMessageForm');
    
    if (notifButtons.length > 0 && typeof bootstrap !== 'undefined') {
        notifButtons.forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                const title = this.getAttribute('data-title');
                const message = this.getAttribute('data-message');
                const url = this.getAttribute('data-action');
                
                notifTitle.textContent = title;
                notifContent.textContent = message;
                notifForm.action = url;
                
                const modal = new bootstrap.Modal(document.getElementById('notifMessageModal'));
                modal.show();
            });
        });
    }
});
</script>

</body>
</html>
