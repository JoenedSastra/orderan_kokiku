<a href="{{ route('kasir.dashboard') }}" class="nav-link {{ request()->routeIs('kasir.dashboard') ? 'active' : '' }}">
    <i class="bi bi-grid-1x2"></i> Dashboard
</a>

<div class="kk-sidebar-section-label">Stok</div>
<a href="{{ route('kasir.stock_in.index') }}" class="nav-link {{ request()->routeIs('kasir.stock_in.*') ? 'active' : '' }}">
    <i class="bi bi-box-arrow-in-down"></i> Stock Barang Masuk
</a>
<a href="{{ route('kasir.stock_out.index') }}" class="nav-link {{ request()->routeIs('kasir.stock_out.*') ? 'active' : '' }}">
    <i class="bi bi-box-arrow-up"></i> Stock Barang Keluar
</a>

<div class="kk-sidebar-section-label">Permintaan</div>
<a href="{{ route('kasir.orders.index') }}" class="nav-link {{ request()->routeIs('kasir.orders.*') ? 'active' : '' }}">
    <i class="bi bi-clipboard-plus"></i> Order Barang
</a>
