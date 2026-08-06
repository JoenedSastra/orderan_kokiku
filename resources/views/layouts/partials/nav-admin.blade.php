<a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
    <i class="bi bi-grid-1x2"></i> Dashboard
</a>

<div class="kk-sidebar-section-label">Master Data</div>
<a href="#" class="nav-link disabled"><i class="bi bi-box-seam"></i> Master Barang</a>
<a href="#" class="nav-link disabled"><i class="bi bi-tags"></i> Kategori</a>
<a href="#" class="nav-link disabled"><i class="bi bi-truck"></i> Supplier</a>

<div class="kk-sidebar-section-label">Stok & Permintaan</div>
<a href="#" class="nav-link disabled"><i class="bi bi-layers"></i> Stock Barang</a>
<a href="#" class="nav-link disabled"><i class="bi bi-clipboard-check"></i> Permintaan Barang</a>

<div class="kk-sidebar-section-label">Laporan</div>
<a href="#" class="nav-link disabled"><i class="bi bi-clock-history"></i> Riwayat</a>
<a href="#" class="nav-link disabled"><i class="bi bi-bar-chart"></i> Laporan</a>

<div class="kk-sidebar-section-label">Pengaturan</div>
<a href="#" class="nav-link disabled"><i class="bi bi-people"></i> User</a>
<a href="#" class="nav-link disabled"><i class="bi bi-shield-lock"></i> Role</a>
