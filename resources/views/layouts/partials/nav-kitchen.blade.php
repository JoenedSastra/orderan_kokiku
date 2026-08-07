<a href="{{ route('kitchen.dashboard') }}" class="nav-link {{ request()->routeIs('kitchen.dashboard') ? 'active' : '' }}">
    <i class="bi bi-grid-1x2"></i> Dashboard
</a>

<div class="kk-sidebar-section-label">Stok</div>
<a href="{{ route('kitchen.stock_in.index') }}" class="nav-link {{ request()->routeIs('kitchen.stock_in.*') ? 'active' : '' }}">
    <i class="bi bi-box-arrow-in-down"></i> Stock Barang Masuk
</a>
<a href="{{ route('kitchen.stock_out.index') }}" class="nav-link {{ request()->routeIs('kitchen.stock_out.*') ? 'active' : '' }}">
    <i class="bi bi-box-arrow-up"></i> Stock Barang Keluar
</a>

<a href="{{ route('kitchen.stock_harian.index') }}" class="nav-link {{ request()->routeIs('kitchen.stock_harian.*') ? 'active' : '' }}">
    <i class="bi bi-calendar3"></i> Stock Harian
</a>

<div class="kk-sidebar-section-label">Permintaan</div>
<a href="{{ route('kitchen.orders.index') }}" class="nav-link {{ request()->routeIs('kitchen.orders.*') ? 'active' : '' }}">
    <i class="bi bi-clipboard-plus"></i> Order Barang
</a>
