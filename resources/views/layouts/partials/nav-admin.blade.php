<div class="kk-sidebar-section-label">Utama</div>
<a href="{{ route('admin.dashboard') }}" class="kk-devisi-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" style="--accent-color:#6366f1; --icon-glow:rgba(99,102,241,0.45);">
    <div class="devisi-icon" style="background:linear-gradient(135deg,rgba(99,102,241,0.35),rgba(99,102,241,0.15)); color:#818cf8; border-color:rgba(99,102,241,0.3);">
        <i class="bi bi-grid-fill"></i>
    </div>
    <span class="devisi-label">
        Dashbord
        <span class="devisi-sub">Ringkasan sistem</span>
    </span>
</a>

<a href="{{ route('admin.stock_in.index') }}" class="kk-devisi-link {{ request()->routeIs('admin.stock_in.*') ? 'active' : '' }}" style="--accent-color:#38bdf8; --icon-glow:rgba(56,189,248,0.45);">
    <div class="devisi-icon" style="background:linear-gradient(135deg,rgba(56,189,248,0.35),rgba(56,189,248,0.15)); color:#38bdf8; border-color:rgba(56,189,248,0.3);">
        <i class="bi bi-box-arrow-in-down-right"></i>
    </div>
    <span class="devisi-label">
        Barang Masuk
        <span class="devisi-sub">Input harian</span>
    </span>
</a>

<div class="kk-sidebar-section-label">Devisi</div>

<style>
/* ===== Devisi Nav Links ===== */
.kk-devisi-link {
    position: relative;
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 0.32rem 0.75rem;
    border-radius: 10px;
    text-decoration: none;
    color: rgba(255,255,255,0.72);
    font-size: 0.82rem;
    font-weight: 500;
    transition: background 0.22s, color 0.22s, transform 0.18s, box-shadow 0.22s;
    margin-bottom: 2px;
    overflow: hidden;
}
.kk-devisi-link:hover {
    background: rgba(255,255,255,0.07);
    color: #fff;
    transform: translateX(4px);
}
.kk-devisi-link.active {
    background: rgba(255,255,255,0.10);
    color: #fff;
}
/* Left accent bar */
.kk-devisi-link::before {
    content: '';
    position: absolute;
    left: 0; top: 15%; bottom: 15%;
    width: 3px;
    border-radius: 0 4px 4px 0;
    background: transparent;
    transition: background 0.22s, box-shadow 0.22s;
}
.kk-devisi-link.active::before {
    background: var(--accent-color, #facc15);
    box-shadow: 0 0 6px var(--accent-color, #facc15);
}
/* Icon box */
.kk-devisi-link .devisi-icon {
    width: 28px; height: 28px;
    border-radius: 8px;
    display: flex; align-items: center; justify-content: center;
    font-size: 0.82rem;
    flex-shrink: 0;
    border: 1px solid var(--icon-border, rgba(255,255,255,0.12));
    box-shadow: 0 2px 6px rgba(0,0,0,0.2);
    transition: transform 0.22s, box-shadow 0.22s;
    position: relative;
    overflow: hidden;
}
.kk-devisi-link .devisi-icon::after {
    content: '';
    position: absolute;
    inset: 0;
    background: linear-gradient(135deg, rgba(255,255,255,0.25) 0%, transparent 60%);
    opacity: 0;
    transition: opacity 0.22s;
}
.kk-devisi-link:hover .devisi-icon::after,
.kk-devisi-link.active .devisi-icon::after { opacity: 1; }
.kk-devisi-link:hover .devisi-icon,
.kk-devisi-link.active .devisi-icon {
    transform: scale(1.12) rotate(-4deg);
    box-shadow: 0 4px 14px var(--icon-glow, rgba(255,255,255,0.15));
}
.kk-devisi-link .devisi-label { flex: 1; line-height: 1.2; }
.kk-devisi-link .devisi-sub {
    font-size: 0.68rem;
    opacity: 0.55;
    display: block;
    transition: opacity 0.2s;
}
.kk-devisi-link:hover .devisi-sub,
.kk-devisi-link.active .devisi-sub { opacity: 0.8; }
</style>

{{-- Gudang Utama --}}
<a href="{{ route('admin.stock.index', ['filter' => 'gudang_utama']) }}"
   class="kk-devisi-link {{ request()->routeIs('admin.stock.index') && request('filter') === 'gudang_utama' ? 'active' : '' }}"
   style="--accent-color: #f97316; --icon-glow: rgba(249,115,22,0.45);">
    <div class="devisi-icon" style="background: linear-gradient(135deg,rgba(249,115,22,0.35),rgba(249,115,22,0.15)); color:#f97316; border-color:rgba(249,115,22,0.3);">
        <i class="bi bi-building"></i>
    </div>
    <span class="devisi-label">
        Gudang Utama
        <span class="devisi-sub">Stok barang utama</span>
    </span>
</a>

{{-- Gudang Resto --}}
<a href="{{ route('admin.stock.index', ['filter' => 'gudang_resto']) }}"
   class="kk-devisi-link {{ request()->routeIs('admin.stock.index') && request('filter') === 'gudang_resto' ? 'active' : '' }}"
   style="--accent-color: #22c55e; --icon-glow: rgba(34,197,94,0.45);">
    <div class="devisi-icon" style="background: linear-gradient(135deg,rgba(34,197,94,0.35),rgba(34,197,94,0.15)); color:#22c55e; border-color:rgba(34,197,94,0.3);">
        <i class="bi bi-shop"></i>
    </div>
    <span class="devisi-label">
        Gudang Resto
        <span class="devisi-sub">Stok restoran</span>
    </span>
</a>

{{-- Kasir --}}
<a href="{{ route('admin.stock_kasir_kitchen.index', ['filter' => 'kasir']) }}"
   class="kk-devisi-link {{ request()->routeIs('admin.stock_kasir_kitchen.*') && request('filter') === 'kasir' ? 'active' : '' }}"
   style="--accent-color: #ef4444; --icon-glow: rgba(239,68,68,0.45);">
    <div class="devisi-icon" style="background: linear-gradient(135deg,rgba(239,68,68,0.35),rgba(239,68,68,0.15)); color:#ef4444; border-color:rgba(239,68,68,0.3);">
        <i class="bi bi-cash-coin"></i>
    </div>
    <span class="devisi-label">
        Kasir
        <span class="devisi-sub">Stok kasir</span>
    </span>
</a>

<a href="{{ route('admin.stock_kasir_kitchen.index', ['filter' => 'kitchen']) }}"
   class="kk-devisi-link {{ request()->routeIs('admin.stock_kasir_kitchen.*') && request('filter') === 'kitchen' ? 'active' : '' }}"
   style="--accent-color: #eab308; --icon-glow: rgba(234,179,8,0.45);">
    <div class="devisi-icon" style="background: linear-gradient(135deg,rgba(234,179,8,0.35),rgba(234,179,8,0.15)); color:#eab308; border-color:rgba(234,179,8,0.3);">
        <svg xmlns="http://www.w3.org/2000/svg" width="1.2em" height="1.2em" viewBox="0 0 24 24" fill="currentColor">
            <path d="M12 4c-4.42 0-8 3.58-8 8v1a1 1 0 0 0 1 1h16.29l3.42 3.41 1.41-1.41L21 14.29V13c0-4.42-3.58-8-8-9m0 2c3.31 0 6 2.69 6 6v1H6v-1c0-3.31 2.69-6 6-6z" />
        </svg>
    </div>
    <span class="devisi-label">
        Kitchen
        <span class="devisi-sub">Stok dapur</span>
    </span>
</a>

<div class="kk-sidebar-section-label">Laporan</div>

<a href="{{ route('admin.reports.index') }}" class="kk-devisi-link {{ request()->routeIs('admin.reports.*') ? 'active' : '' }}" style="--accent-color:#a855f7; --icon-glow:rgba(168,85,247,0.45);">
    <div class="devisi-icon" style="background:linear-gradient(135deg,rgba(168,85,247,0.35),rgba(168,85,247,0.15)); color:#a855f7; border-color:rgba(168,85,247,0.3);">
        <i class="bi bi-bar-chart-fill"></i>
    </div>
    <span class="devisi-label">
        Laporan
        <span class="devisi-sub">Ekspor & rekap</span>
    </span>
</a>

<a href="{{ route('admin.data_stock.index') }}" class="kk-devisi-link {{ request()->routeIs('admin.data_stock.*') ? 'active' : '' }}" style="--accent-color:#06b6d4; --icon-glow:rgba(6,182,212,0.45);">
    <div class="devisi-icon" style="background:linear-gradient(135deg,rgba(6,182,212,0.35),rgba(6,182,212,0.15)); color:#06b6d4; border-color:rgba(6,182,212,0.3);">
        <i class="bi bi-box-seam"></i>
    </div>
    <span class="devisi-label">
        Data Stock
        <span class="devisi-sub">Stok per devisi</span>
    </span>
</a>

<div class="kk-sidebar-section-label">Pengguna</div>
<a href="{{ route('admin.users.index') }}" class="kk-devisi-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}" style="--accent-color:#ec4899; --icon-glow:rgba(236,72,153,0.45);">
    <div class="devisi-icon" style="background:linear-gradient(135deg,rgba(236,72,153,0.35),rgba(236,72,153,0.15)); color:#ec4899; border-color:rgba(236,72,153,0.3);">
        <i class="bi bi-people-fill"></i>
    </div>
    <span class="devisi-label">
        User
        <span class="devisi-sub">Manajemen pengguna</span>
    </span>
</a>
