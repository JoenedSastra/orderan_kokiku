<a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
    <i class="bi bi-grid-1x2"></i> Dashboard
</a>

<div class="kk-sidebar-section-label">Master Data</div>
<a href="{{ route('admin.items.index') }}" class="nav-link {{ request()->routeIs('admin.items.*') ? 'active' : '' }}">
    <i class="bi bi-box-seam"></i> Master Barang
</a>
<a href="{{ route('admin.categories.index') }}" class="nav-link {{ request()->routeIs('admin.categories.*') ? 'active' : '' }}">
    <i class="bi bi-tags"></i> Kategori
</a>
<a href="{{ route('admin.suppliers.index') }}" class="nav-link {{ request()->routeIs('admin.suppliers.*') ? 'active' : '' }}">
    <i class="bi bi-truck"></i> Supplier
</a>

<div class="kk-sidebar-section-label">Stok & Permintaan</div>
<a href="{{ route('admin.stock.index') }}" class="nav-link {{ request()->routeIs('admin.stock.*') ? 'active' : '' }}">
    <i class="bi bi-layers"></i> Stock Barang
</a>
<a href="{{ route('admin.stock_in.index') }}" class="nav-link {{ request()->routeIs('admin.stock_in.*') ? 'active' : '' }}">
    <i class="bi bi-truck-flatbed"></i> Barang Masuk Gudang
</a>

<a href="{{ route('admin.stock_kitchen.index') }}" class="nav-link {{ request()->routeIs('admin.stock_kitchen.*') ? 'active' : '' }}">
    <i class="bi bi-calendar3"></i> Stock Kitchen Harian
</a>

<a href="{{ route('admin.orders.index') }}" class="nav-link {{ request()->routeIs('admin.orders.*') ? 'active' : '' }}">
    <i class="bi bi-clipboard-check"></i> Permintaan Barang
    @php $pending = \App\Models\Order::where('status','menunggu')->count(); @endphp
    @if($pending > 0)
        <span class="badge bg-danger ms-auto rounded-pill">{{ $pending }}</span>
    @endif
</a>

<div class="kk-sidebar-section-label">Pengaturan</div>
<a href="{{ route('admin.users.index') }}" class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
    <i class="bi bi-people"></i> User
</a>

<a href="{{ route('admin.reports.index') }}" class="nav-link {{ request()->routeIs('admin.reports.*') ? 'active' : '' }}">
    <i class="bi bi-file-earmark-bar-graph"></i> Laporan
</a>
