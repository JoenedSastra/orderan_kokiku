@php
    $unreadCount   = auth()->user()->unreadNotifications->count();
    $notifications = auth()->user()->notifications()->latest()->limit(8)->get();
@endphp

<div class="dropdown kk-notif-dropdown">
    <button class="btn position-relative" type="button"
            data-bs-toggle="dropdown" aria-expanded="false"
            title="Notifikasi">
        @if($unreadCount > 0)
            <span class="kk-bell-ring"><i class="bi bi-bell-fill" style="color:var(--kk-orange);"></i></span>
            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger"
                  style="font-size:0.58rem; padding:0.22rem 0.4rem;">
                {{ $unreadCount > 9 ? '9+' : $unreadCount }}
            </span>
        @else
            <i class="bi bi-bell"></i>
        @endif
    </button>

    <div class="dropdown-menu dropdown-menu-end kk-notif-menu">
        {{-- Header --}}
        <div class="kk-notif-header">
            <div class="d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center gap-2">
                    <span class="kk-notif-title">Notifikasi</span>
                    @if($unreadCount > 0)
                        <span class="kk-notif-count">{{ $unreadCount }} baru</span>
                    @endif
                </div>
                @if($unreadCount > 0)
                    <form method="POST" action="{{ route('notifications.read-all') }}" class="mb-0">
                        @csrf
                        <button class="btn btn-link p-0" style="font-size:0.72rem; color:rgba(255,255,255,0.55);">
                            Tandai semua dibaca
                        </button>
                    </form>
                @endif
            </div>
        </div>

        {{-- Notification Items --}}
        @forelse($notifications as $notif)
        <form method="POST" action="{{ route('notifications.read', $notif->id) }}">
            @csrf
            <button type="submit" class="dropdown-item {{ $notif->read_at ? '' : 'kk-notif-unread' }}">
                <div class="d-flex gap-2 align-items-start">
                    <div style="width:30px; height:30px; border-radius:50%; background:var(--kk-orange-soft); color:var(--kk-orange); display:flex; align-items:center; justify-content:center; font-size:0.8rem; flex-shrink:0; margin-top:1px;">
                        <i class="bi bi-bell"></i>
                    </div>
                    <div style="min-width:0; flex:1;">
                        <div class="fw-semibold text-truncate" style="font-size:0.82rem; color:var(--kk-text);">{{ $notif->data['title'] ?? '-' }}</div>
                        <div class="text-muted" style="font-size:0.76rem; white-space:normal; margin-top:1px;">{{ $notif->data['message'] ?? '' }}</div>
                        <div style="font-size:0.68rem; color:var(--kk-orange); margin-top:3px; font-weight:600;">
                            <i class="bi bi-clock me-1"></i>{{ $notif->created_at->diffForHumans() }}
                        </div>
                    </div>
                    @if(!$notif->read_at)
                        <div style="width:7px; height:7px; background:var(--kk-orange); border-radius:50%; flex-shrink:0; margin-top:5px;"></div>
                    @endif
                </div>
            </button>
        </form>
        @empty
        <div class="px-3 py-5 text-center">
            <div style="font-size:2rem; color:var(--kk-border); margin-bottom:0.5rem;"><i class="bi bi-bell-slash"></i></div>
            <div style="font-size:0.82rem; color:var(--kk-text-light);">Belum ada notifikasi</div>
        </div>
        @endforelse
    </div>
</div>
