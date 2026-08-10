<a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
    <i class="bi bi-grid-fill"></i> Dashbord
</a>

<a href="{{ route('admin.stock_in.index') }}" class="nav-link {{ request()->routeIs('admin.stock_in.*') ? 'active' : '' }}">
    <i class="bi bi-box-arrow-in-down-right"></i> Barang Masuk Harian
</a>

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

<a href="{{ route('admin.users.index') }}" class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
    <i class="bi bi-people-fill"></i> User
</a>
