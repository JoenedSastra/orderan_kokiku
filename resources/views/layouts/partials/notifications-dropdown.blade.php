@php
    $unreadCount   = auth()->user()->unreadNotifications->count();
    $notifications = auth()->user()->notifications()->latest()->limit(8)->get();
    $hasRead       = auth()->user()->notifications()->whereNotNull('read_at')->exists();
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

    <div class="dropdown-menu dropdown-menu-end kk-notif-menu" style="min-width: 320px; max-width: 100vw;">
        {{-- Header --}}
        <div class="kk-notif-header">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                <div class="d-flex align-items-center gap-2 flex-shrink-0">
                    <span class="kk-notif-title">Notifikasi</span>
                    @if($unreadCount > 0)
                        <span class="badge rounded-pill bg-primary" style="font-size: 0.75rem; font-weight: 500; padding: 0.35em 0.65em;">{{ $unreadCount }} baru</span>
                    @endif
                </div>
                @if($notifications->count() > 0)
                    <div class="d-flex justify-content-end gap-1 flex-wrap">
                        @if($unreadCount > 0)
                            <form method="POST" action="{{ route('notifications.read-all') }}" class="m-0 d-inline-block">
                                @csrf
                                <button type="submit" class="btn text-white px-2 py-1" style="background-color: #1e40af; font-size:0.75rem; border-radius: 4px; border: none; white-space: nowrap; width: max-content !important; display: inline-block;">
                                    Tandai dibaca
                                </button>
                            </form>
                        @endif
                        @if($hasRead)
                            <form method="POST" action="{{ route('notifications.destroy-all-read') }}" class="m-0 d-inline-block">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger text-white px-2 py-1" style="font-size:0.75rem; border-radius: 4px; border: none; white-space: nowrap; width: max-content !important; display: inline-block;">
                                    Hapus Semua
                                </button>
                            </form>
                        @endif
                    </div>
                @endif
            </div>
        </div>

        {{-- Notification Items --}}
        @forelse($notifications as $notif)
        <div class="dropdown-item position-relative d-flex align-items-center p-0 {{ $notif->read_at ? '' : 'kk-notif-unread' }}" style="padding-right: 2.5rem !important;">
            <button type="button" class="kk-notif-modal-btn w-100 text-start bg-transparent border-0 p-3"
                    data-title="{{ $notif->data['title'] ?? '-' }}"
                    data-message="{{ $notif->data['message'] ?? '' }}"
                    data-action="{{ route('notifications.read', $notif->id) }}"
                    style="outline: none;">
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

            @if($notif->read_at)
                <form method="POST" action="{{ route('notifications.destroy', $notif->id) }}" class="position-absolute m-0" style="right: 15px; top: 50%; transform: translateY(-50%); z-index: 10;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn text-danger p-1" title="Hapus Notifikasi" style="background:transparent; border:none; width:auto; height:auto; border-radius:4px;">
                        <i class="bi bi-trash" style="font-size: 1.1rem;"></i>
                    </button>
                </form>
            @endif
        </div>
        @empty
        <div class="px-3 py-5 text-center">
            <div style="font-size:2rem; color:var(--kk-border); margin-bottom:0.5rem;"><i class="bi bi-bell-slash"></i></div>
            <div style="font-size:0.82rem; color:var(--kk-text-light);">Belum ada notifikasi</div>
        </div>
        @endforelse
    </div>
</div>


