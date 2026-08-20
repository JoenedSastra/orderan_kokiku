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

<a href="{{ route('kitchen.dashboard') }}" class="kk-devisi-link {{ request()->routeIs('kitchen.dashboard') ? 'active' : '' }}" style="--accent-color:#6366f1; --icon-glow:rgba(99,102,241,0.45);">
    <div class="devisi-icon" style="background:linear-gradient(135deg,rgba(99,102,241,0.35),rgba(99,102,241,0.15)); color:#818cf8; border-color:rgba(99,102,241,0.3);">
        <i class="bi bi-grid-fill"></i>
    </div>
    <span class="devisi-label">
        Dashboard
        <span class="devisi-sub">Ringkasan sistem</span>
    </span>
</a>

<div class="kk-sidebar-section-label mt-2">Manajemen Stok</div>
<a href="{{ route('kitchen.stock_in.index') }}" class="kk-devisi-link {{ request()->routeIs('kitchen.stock_in.*') ? 'active' : '' }}" style="--accent-color:#38bdf8; --icon-glow:rgba(56,189,248,0.45);">
    <div class="devisi-icon" style="background:linear-gradient(135deg,rgba(56,189,248,0.35),rgba(56,189,248,0.15)); color:#38bdf8; border-color:rgba(56,189,248,0.3);">
        <i class="bi bi-box-arrow-in-down-right"></i>
    </div>
    <span class="devisi-label">
        Barang Masuk
        <span class="devisi-sub">Input harian</span>
    </span>
</a>
<a href="{{ route('kitchen.stock_out.index') }}" class="kk-devisi-link {{ request()->routeIs('kitchen.stock_out.*') ? 'active' : '' }}" style="--accent-color:#f43f5e; --icon-glow:rgba(244,63,94,0.45);">
    <div class="devisi-icon" style="background:linear-gradient(135deg,rgba(244,63,94,0.35),rgba(244,63,94,0.15)); color:#fb7185; border-color:rgba(244,63,94,0.3);">
        <i class="bi bi-box-arrow-up-right"></i>
    </div>
    <span class="devisi-label">
        Barang Keluar
        <span class="devisi-sub">Stok keluar</span>
    </span>
</a>

<a href="{{ route('kitchen.stock_harian.index') }}" class="kk-devisi-link {{ request()->routeIs('kitchen.stock_harian.*') ? 'active' : '' }}" style="--accent-color:#10b981; --icon-glow:rgba(16,185,129,0.45);">
    <div class="devisi-icon" style="background:linear-gradient(135deg,rgba(16,185,129,0.35),rgba(16,185,129,0.15)); color:#34d399; border-color:rgba(16,185,129,0.3);">
        <i class="bi bi-bar-chart-line-fill"></i>
    </div>
    <span class="devisi-label">
        Total Stock Barang
        <span class="devisi-sub">Rekapitulasi stok</span>
    </span>
</a>

<div class="kk-sidebar-section-label mt-2">Permintaan</div>
<a href="{{ route('kitchen.orders.create') }}" class="kk-devisi-link {{ request()->routeIs('kitchen.orders.create') ? 'active' : '' }}" style="--accent-color:#f59e0b; --icon-glow:rgba(245,158,11,0.45);">
    <div class="devisi-icon" style="background:linear-gradient(135deg,rgba(245,158,11,0.35),rgba(245,158,11,0.15)); color:#fbbf24; border-color:rgba(245,158,11,0.3);">
        <i class="bi bi-cart-plus"></i>
    </div>
    <span class="devisi-label">
        Order Barang
        <span class="devisi-sub">Buat permintaan</span>
    </span>
</a>
