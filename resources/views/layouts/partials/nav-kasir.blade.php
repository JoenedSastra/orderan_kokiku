<a href="{{ route('kasir.dashboard') }}" class="nav-link {{ request()->routeIs('kasir.dashboard') ? 'active' : '' }}">
    <i class="bi bi-grid-1x2"></i> Dashboard
</a>

<div class="kk-sidebar-section-label">Stok</div>
<a href="#" class="nav-link disabled"><i class="bi bi-box-arrow-in-down"></i> Stock Barang Masuk</a>
<a href="#" class="nav-link disabled"><i class="bi bi-box-arrow-up"></i> Stock Barang Keluar</a>

<div class="kk-sidebar-section-label">Permintaan</div>
<a href="#" class="nav-link disabled"><i class="bi bi-clipboard-plus"></i> Order Barang</a>
<a href="#" class="nav-link disabled"><i class="bi bi-clock-history"></i> Riwayat</a>

<div class="kk-sidebar-section-label">Akun</div>
<a href="#" class="nav-link disabled"><i class="bi bi-person"></i> Profil</a>
