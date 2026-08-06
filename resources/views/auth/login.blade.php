@extends('layouts.guest')

@section('title', 'Login')

@section('content')
<div class="kk-auth-wrapper">
    <div class="kk-auth-card">
        <div class="text-center mb-4">
            <div class="fw-bold fs-4">Orderan Kokiku</div>
            <div class="text-muted" style="font-size: 0.85rem;">Sistem Order Barang Internal</div>
        </div>

        @if ($errors->any())
            <div class="alert alert-danger py-2" style="font-size: 0.88rem;">
                @foreach ($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" id="email" name="email" value="{{ old('email') }}"
                       class="form-control @error('email') is-invalid @enderror"
                       required autofocus>
            </div>

            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" id="password" name="password"
                       class="form-control @error('password') is-invalid @enderror"
                       required>
            </div>

            <div class="mb-3 form-check">
                <input type="checkbox" name="remember" id="remember" class="form-check-input">
                <label for="remember" class="form-check-label" style="font-size: 0.88rem;">Ingat saya</label>
            </div>

            <button type="submit" class="btn w-100 text-white" style="background-color: var(--kk-accent);">
                Masuk
            </button>
        </form>
    </div>
</div>
@endsection
