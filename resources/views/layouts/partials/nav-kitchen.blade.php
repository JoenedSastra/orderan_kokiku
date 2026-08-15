<a href="{{ route('kitchen.dashboard') }}" class="nav-link {{ request()->routeIs('kitchen.dashboard') ? 'active' : '' }}">
    <i class="bi bi-grid-fill"></i> Dashboard
</a>

<div class="kk-sidebar-section-label">Manajemen Stok</div>
<a href="{{ route('kitchen.stock_in.index') }}" class="nav-link {{ request()->routeIs('kitchen.stock_in.*') ? 'active' : '' }}">
    <i class="bi bi-box-arrow-in-down-right"></i> Barang Masuk
</a>
<a href="{{ route('kitchen.stock_out.index') }}" class="nav-link {{ request()->routeIs('kitchen.stock_out.*') ? 'active' : '' }}">
    <i class="bi bi-box-arrow-up-right"></i> Barang Keluar
</a>

<a href="{{ route('kitchen.stock_harian.index') }}" class="nav-link {{ request()->routeIs('kitchen.stock_harian.*') ? 'active' : '' }}">
    <i class="bi bi-bar-chart-line-fill"></i> Total Stock Barang
</a>

<div class="kk-sidebar-section-label">Permintaan</div>
<a href="{{ route('kitchen.orders.index') }}" class="nav-link {{ request()->routeIs('kitchen.orders.*') ? 'active' : '' }}">
    <i class="bi bi-clipboard2-plus-fill"></i> Order Barang
</a>
