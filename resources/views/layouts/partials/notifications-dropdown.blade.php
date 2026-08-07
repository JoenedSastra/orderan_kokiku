@php
    $unreadCount = auth()->user()->unreadNotifications->count();
    $notifications = auth()->user()->notifications()->latest()->limit(8)->get();
@endphp

<div class="dropdown kk-notif-dropdown">
    <button class="btn btn-outline-secondary btn-sm position-relative" type="button" data-bs-toggle="dropdown" aria-expanded="false">
        <i class="bi bi-bell"></i>
        @if($unreadCount > 0)
            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size:0.6rem;">
                {{ $unreadCount > 9 ? '9+' : $unreadCount }}
            </span>
        @endif
    </button>

    <div class="dropdown-menu dropdown-menu-end p-0 kk-notif-menu">
        <div class="d-flex justify-content-between align-items-center px-3 py-2 border-bottom">
            <span class="fw-semibold" style="font-size:0.85rem;">Notifikasi</span>
            @if($unreadCount > 0)
            <form method="POST" action="{{ route('notifications.read-all') }}">
                @csrf
                <button class="btn btn-link btn-sm p-0" style="font-size:0.75rem;">Tandai semua dibaca</button>
            </form>
            @endif
        </div>

        @forelse($notifications as $notif)
        <form method="POST" action="{{ route('notifications.read', $notif->id) }}">
            @csrf
            <button type="submit" class="dropdown-item py-2 border-bottom {{ $notif->read_at ? '' : 'kk-notif-unread' }}">
                <div class="fw-semibold" style="font-size:0.82rem;">{{ $notif->data['title'] ?? '-' }}</div>
                <div class="text-muted" style="font-size:0.78rem; white-space:normal;">{{ $notif->data['message'] ?? '' }}</div>
                <div class="text-muted" style="font-size:0.7rem;">{{ $notif->created_at->diffForHumans() }}</div>
            </button>
        </form>
        @empty
        <div class="px-3 py-4 text-center text-muted" style="font-size:0.82rem;">Belum ada notifikasi.</div>
        @endforelse
    </div>
</div>
