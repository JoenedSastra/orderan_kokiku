@extends('layouts.guest')
@section('title', 'Masuk')

@section('content')
<div class="kk-auth-wrapper">

    {{-- LEFT PANEL --}}
    <div class="kk-auth-left">
        {{-- Brand --}}
        <div class="kk-auth-brand">
            <div class="kk-auth-brand-icon">
                <img src="{{ asset('images/logo-kokiku.jpeg') }}" alt="Logo Kokiku">
            </div>
            <div>
                <div class="kk-auth-brand-name">Myhub Kokiku</div>
                <div class="kk-auth-brand-sub">Inventory System</div>
            </div>
        </div>

        {{-- Headline --}}
        <h1 class="kk-auth-headline">
            Kelola Semua<br>
            <span>Stock Anda</span><br>
            Dengan Mudah
        </h1>

        <p class="kk-auth-sub">
            Platform manajemen inventaris operasional resto
            — real-time, cepat, dan andal.
        </p>
    </div>

    {{-- RIGHT PANEL (Form) --}}
    <div class="kk-auth-right">
        <div class="kk-auth-form-wrap">

            <div class="kk-auth-form-title">Selamat Datang 👋</div>
            <div class="kk-auth-form-sub">Masuk ke akun Anda untuk melanjutkan</div>

            @if ($errors->any())
                <div class="alert alert-danger py-2 mb-4" style="font-size:0.85rem;">
                    <i class="bi bi-exclamation-circle me-1"></i>
                    @foreach ($errors->all() as $error)
                        <div>{{ $error }}</div>
                    @endforeach
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}">
                @csrf

                <div class="kk-form-group">
                    <label for="email">Alamat Email</label>
                    <input type="email" id="email" name="email"
                           value="{{ old('email') }}"
                           class="form-control @error('email') is-invalid @enderror"
                           required autofocus>
                </div>

                <div class="kk-form-group">
                    <label for="password">Password</label>
                    <div class="kk-pw-wrap">
                        <input type="password" id="password" name="password"
                               class="form-control @error('password') is-invalid @enderror"
                               required>
                        <button type="button" class="kk-pw-toggle" id="pwToggle" aria-label="Tampilkan password">
                            <i class="bi bi-eye" id="pwToggleIcon"></i>
                        </button>
                    </div>
                </div>

                <div class="d-flex align-items-center justify-content-between mb-4" style="font-size:0.84rem;">
                    <div class="form-check mb-0">
                        <input type="checkbox" name="remember" id="remember" class="form-check-input">
                        <label for="remember" class="form-check-label text-muted">Ingat saya</label>
                    </div>
                </div>

                <button type="submit" class="kk-btn-login" id="loginBtn">
                    <i class="bi bi-box-arrow-in-right me-2"></i>Masuk
                </button>
            </form>

            <div class="text-center mt-4" style="font-size:0.78rem; color:var(--kk-text-light);">
                Myhub Kokiku Sistem Inventory Internal
            </div>
        </div>
    </div>
</div>

<script>
// Password toggle
document.getElementById('pwToggle').addEventListener('click', function () {
    const pw   = document.getElementById('password');
    const icon = document.getElementById('pwToggleIcon');
    if (pw.type === 'password') {
        pw.type = 'text';
        icon.className = 'bi bi-eye-slash';
    } else {
        pw.type = 'password';
        icon.className = 'bi bi-eye';
    }
});

// Loading state on submit
document.querySelector('form').addEventListener('submit', function () {
    const btn = document.getElementById('loginBtn');
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Memproses...';
    btn.disabled = true;
});
</script>
@endsection
