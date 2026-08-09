<a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
    <i class="bi bi-grid-fill"></i> Dashbord
</a>

<a href="{{ route('admin.stock_in.index') }}" class="nav-link {{ request()->routeIs('admin.stock_in.*') ? 'active' : '' }}">
    <i class="bi bi-box-arrow-in-down-right"></i> Barang Masuk Harian
</a>

<a href="{{ route('admin.items.index') }}" class="nav-link {{ request()->routeIs('admin.items.*') ? 'active' : '' }}">
    <i class="bi bi-box-seam-fill"></i> Master Barang
</a>

<a href="{{ route('admin.stock.index') }}" class="nav-link {{ request()->routeIs('admin.stock.*') ? 'active' : '' }}">
    <i class="bi bi-layers-fill"></i> Stock Gudang & Resto
</a>

<a href="{{ route('admin.stock.index') }}" class="nav-link {{ request()->routeIs('admin.stock.*') ? 'active' : '' }}">
    <i class="bi bi-layers-fill"></i> Stock Kasir & Kitchen
</a>

<a href="{{ route('admin.stock_kitchen.index') }}" class="nav-link {{ request()->routeIs('admin.stock_kitchen.*') ? 'active' : '' }}">
    <i class="bi bi-calendar-check-fill"></i> Stock Kitchen Harian
</a>

<a href="{{ route('admin.orders.index') }}" class="nav-link {{ request()->routeIs('admin.orders.*') ? 'active' : '' }}">
    <i class="bi bi-clipboard2-check-fill"></i> Orderan Kasir & Kitchen
    @php $pending = \App\Models\Order::where('status','menunggu')->count(); @endphp
    @if($pending > 0)
        <span class="badge ms-auto rounded-pill" style="background:var(--kk-orange); font-size:0.65rem;">{{ $pending }}</span>
    @endif
</a>

<a href="{{ route('admin.reports.index') }}" class="nav-link {{ request()->routeIs('admin.reports.*') ? 'active' : '' }}">
    <i class="bi bi-bar-chart-fill"></i> Laporan
</a>

<a href="{{ route('admin.categories.index') }}" class="nav-link {{ request()->routeIs('admin.categories.*') ? 'active' : '' }}">
    <i class="bi bi-tags-fill"></i> Kategori
</a>

<a href="{{ route('admin.suppliers.index') }}" class="nav-link {{ request()->routeIs('admin.suppliers.*') ? 'active' : '' }}">
    <i class="bi bi-truck-front-fill"></i> Supplalier
</a>

<a href="{{ route('admin.users.index') }}" class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
    <i class="bi bi-people-fill"></i> User
</a>
