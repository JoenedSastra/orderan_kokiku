@extends('layouts.app')
@section('title', 'Daftar User')
@section('content')
<h2 class="h5 mb-3">Daftar User</h2>
<div class="kk-stat-card">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr><th>#</th><th>Nama</th><th>Email</th><th>Role</th><th>Dibuat</th></tr>
            </thead>
            <tbody>
                @forelse($users as $user)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $user->name }}</td>
                    <td>{{ $user->email }}</td>
                    <td><span class="badge bg-secondary">{{ $user->role?->name ?? '-' }}</span></td>
                    <td>{{ $user->created_at->format('d-m-Y') }}</td>
                </tr>
                @empty
                <tr><td colspan="5" class="text-center text-muted py-3">Belum ada user.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-3">{{ $users->links() }}</div>
</div>
@endsection
