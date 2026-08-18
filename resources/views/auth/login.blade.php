@extends('layouts.guest')
@section('title', 'Masuk')

@section('content')
<div class="kk-auth-wrapper glass-theme">
    


    {{-- Center Glass Card --}}
    <div class="kk-auth-glass-card">
        
        {{-- Brand / Logo --}}
        <div class="kk-glass-brand">
            <div class="kk-glass-brand-icon">
                <img src="{{ asset('images/logo-kokiku.jpeg') }}" alt="Logo Kokiku">
            </div>
            <div>
                <h1 class="kk-glass-title">Myhub Kokiku</h1>
                <p class="kk-glass-subtitle">Inventory System</p>
            </div>
        </div>

        <div class="kk-glass-welcome">
            <h2>Welcome Back</h2>
            <p>Silakan masuk ke akun Anda</p>
        </div>

        @if ($errors->any())
            <div class="kk-glass-alert">
                <i class="bi bi-exclamation-triangle-fill"></i>
                <div>
                    @foreach ($errors->all() as $error)
                        <div>{{ $error }}</div>
                    @endforeach
                </div>
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}" class="kk-glass-form">
            @csrf

            <div class="kk-glass-form-group">
                <label for="email">Alamat Email</label>
                <div class="input-wrapper">
                    <i class="bi bi-envelope-fill input-icon"></i>
                    <input type="email" id="email" name="email"
                           value="{{ old('email') }}"
                           class="glass-input @error('email') is-invalid @enderror"
                           placeholder="Email@kokiku.com" required autofocus>
                </div>
            </div>

            <div class="kk-glass-form-group">
                <label for="password">Password</label>
                <div class="input-wrapper">
                    <i class="bi bi-lock-fill input-icon"></i>
                    <input type="password" id="password" name="password"
                           class="glass-input @error('password') is-invalid @enderror"
                           placeholder="••••••••" required>
                    <button type="button" class="glass-pw-toggle" id="pwToggle" aria-label="Tampilkan password">
                        <i class="bi bi-eye" id="pwToggleIcon"></i>
                    </button>
                </div>
            </div>

            <button type="submit" class="glass-btn-submit" id="loginBtn">
                <span>Masuk</span>
            </button>
        </form>

        <div class="kk-glass-footer">
            Myhub Kokiku Sistem Inventory Internal
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
