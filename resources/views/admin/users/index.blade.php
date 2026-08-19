@extends('layouts.app')
@section('title', 'Daftar User')
@section('content')
<h2 class="h5 mb-3">Daftar User</h2>
<div class="row g-4">
    @forelse($users as $user)
        <div class="col-md-4">
            <div class="kk-stat-card gradient-blue h-100 d-flex flex-column" style="border-radius: var(--kk-radius); box-shadow: 0 4px 15px rgba(59, 130, 246, 0.2);">
                <div class="d-flex align-items-center gap-3 mb-3">
                    <div class="kk-user-avatar" style="width: 60px; height: 60px; overflow: hidden; padding: 0; font-size: 1.5rem; border-radius: 50%; border: 2px solid rgba(255, 255, 255, 0.4); background: rgba(255,255,255,0.2);">
                        @if($user->avatar)
                            <img src="{{ asset('storage/' . $user->avatar) }}" alt="Avatar" style="width:100%; height:100%; object-fit:cover;">
                        @else
                            <span>{{ strtoupper(substr($user->name, 0, 2)) }}</span>
                        @endif
                    </div>
                    <div>
                        <h5 class="mb-1 fw-bold">{{ $user->name }}</h5>
                        <span class="badge">{{ $user->role?->name ?? '-' }}</span>
                    </div>
                </div>
                
                <div class="mt-auto">
                    <div class="d-flex justify-content-between mb-2 pb-2 border-bottom" style="font-size: 0.9rem;">
                        <span>Email</span>
                        <span style="font-weight: 500;">{{ $user->email }}</span>
                    </div>
                    <div class="d-flex justify-content-between" style="font-size: 0.9rem;">
                        <span>Dibuat</span>
                        <span style="font-weight: 500;">{{ $user->created_at->format('d M Y') }}</span>
                    </div>
                </div>
            </div>
        </div>
    @empty
        <div class="col-12">
            <div class="kk-stat-card text-center text-muted py-5">
                <i class="bi bi-people" style="font-size: 3rem;"></i>
                <p class="mt-3 mb-0">Belum ada user.</p>
            </div>
        </div>
    @endforelse
</div>
@endsection
