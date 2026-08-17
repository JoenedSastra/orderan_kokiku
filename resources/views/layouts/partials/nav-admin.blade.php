<div class="kk-sidebar-section-label">Utama</div>
<a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
    <i class="bi bi-grid-fill"></i> Dashbord
</a>

<a href="{{ route('admin.stock_in.index') }}" class="nav-link {{ request()->routeIs('admin.stock_in.*') ? 'active' : '' }}">
    <i class="bi bi-box-arrow-in-down-right"></i> Barang Masuk Harian
</a>

<div class="kk-sidebar-section-label">Devisi</div>
<a href="{{ route('admin.stock.index', ['filter' => 'gudang_utama']) }}" class="nav-link {{ request()->routeIs('admin.stock.index') && request('filter') === 'gudang_utama' ? 'active' : '' }}">
    <i class="bi bi-building"></i> Stock Gudang Utama
</a>

<a href="{{ route('admin.stock.index', ['filter' => 'gudang_resto']) }}" class="nav-link {{ request()->routeIs('admin.stock.index') && request('filter') === 'gudang_resto' ? 'active' : '' }}">
    <i class="bi bi-shop"></i> Stock Gudang Resto
</a>

<a href="{{ route('admin.stock_kasir_kitchen.index', ['filter' => 'kasir']) }}" class="nav-link {{ request()->routeIs('admin.stock_kasir_kitchen.*') && request('filter') === 'kasir' ? 'active' : '' }}">
    <i class="bi bi-cash-coin"></i> Stock Kasir
</a>

<a href="{{ route('admin.stock_kasir_kitchen.index', ['filter' => 'kitchen']) }}" class="nav-link {{ request()->routeIs('admin.stock_kasir_kitchen.*') && request('filter') === 'kitchen' ? 'active' : '' }}">
    <i class="bi bi-egg-fried"></i> Stock Kitchen
</a>

<div class="kk-sidebar-section-label">Laporan</div>


<a href="{{ route('admin.reports.index') }}" class="nav-link {{ request()->routeIs('admin.reports.*') ? 'active' : '' }}">
    <i class="bi bi-bar-chart-fill"></i> Laporan
</a>
<div class="kk-sidebar-section-label">Pengguna</div>
<a href="{{ route('admin.users.index') }}" class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
    <i class="bi bi-people-fill"></i> User
</a>
