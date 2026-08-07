<a href="{{ route('kasir.dashboard') }}" class="nav-link {{ request()->routeIs('kasir.dashboard') ? 'active' : '' }}">
    <i class="bi bi-grid-fill"></i> Dashboard
</a>

<div class="kk-sidebar-section-label">Manajemen Stok</div>
<a href="{{ route('kasir.stock_in.index') }}" class="nav-link {{ request()->routeIs('kasir.stock_in.*') ? 'active' : '' }}">
    <i class="bi bi-box-arrow-in-down-right"></i> Barang Masuk
</a>
<a href="{{ route('kasir.stock_out.index') }}" class="nav-link {{ request()->routeIs('kasir.stock_out.*') ? 'active' : '' }}">
    <i class="bi bi-box-arrow-up-right"></i> Barang Keluar
</a>

<div class="kk-sidebar-section-label">Permintaan</div>
<a href="{{ route('kasir.orders.index') }}" class="nav-link {{ request()->routeIs('kasir.orders.*') ? 'active' : '' }}">
    <i class="bi bi-clipboard2-plus-fill"></i> Order Barang
</a>
