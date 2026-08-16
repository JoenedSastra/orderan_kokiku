@extends('layouts.app')
@section('title', 'Riwayat Barang Masuk')
@section('content')

@php
$hariIndo = ['Sunday' => 'Minggu', 'Monday' => 'Senin', 'Tuesday' => 'Selasa', 'Wednesday' => 'Rabu', 'Thursday' => 'Kamis', 'Friday' => 'Jumat', 'Saturday' => 'Sabtu'];
$isToday  = $tanggal->toDateString() === today()->toDateString();

$lokasiOptions = [
    ''              => 'Semua Devisi',
    'gudang_utama'  => 'Gudang Utama',
    'gudang_resto'  => 'Gudang Resto',
    'kasir'         => 'Kasir',
    'kitchen'       => 'Kitchen',
];

// Semua lokasi bisa dihapus oleh admin
$adminDeletable = ['gudang_utama', 'gudang_resto', 'kasir', 'kitchen'];
@endphp

@push('topbar-extra')
<span class="text-muted d-none d-md-flex align-items-center gap-1" style="font-size:.78rem; font-weight:400;">
    <i class="bi bi-calendar3" style="color:var(--kk-orange); font-size:.8rem;"></i>
    <span>{{ $hariIndo[$tanggal->format('l')] }}, {{ $tanggal->format('d-m-Y') }}</span>
    @if($isToday)<span class="badge bg-success">Hari Ini</span>@endif
</span>
@endpush

{{-- Page header: search --}}
<div class="d-flex justify-content-between align-items-center mb-3 kk-page-header flex-wrap gap-2">
    <div class="kk-search-box">
        <i class="bi bi-search"></i>
        <input type="text" name="kk_search" class="form-control form-control-sm kk-search-nama-barang" placeholder="Cari nama barang..." autocomplete="off">
    </div>
</div>

{{-- Toolbar: kalender + tombol --}}
<div class="d-flex flex-wrap align-items-center gap-2 mb-3">

    {{-- Tombol Kembali --}}
    <a href="{{ route('admin.stock_in.index') }}?dari=kembali" class="btn btn-danger btn-sm">
        <i class="bi bi-arrow-left"></i> Kembali
    </a>

    {{-- Kalender --}}
    <form method="GET" id="filterForm" class="d-flex align-items-center gap-2">
        <div class="input-group shadow-sm" style="max-width:220px; border-radius:8px; overflow:hidden;">
            <span class="input-group-text" style="background:#fff3e8; border-color:#fdd9b5; border-right:none;">
                <i class="bi bi-calendar2-event-fill" style="color:var(--kk-orange); font-size:1rem;"></i>
            </span>
            <input type="date" name="tanggal" id="inputTanggal" value="{{ $tanggal->toDateString() }}"
                   class="form-control"
                   style="border-color:#fdd9b5; border-left:none; font-size:.875rem;">
        </div>
        {{-- Pertahankan lokasi saat ganti tanggal --}}
        @if(request('lokasi'))
            <input type="hidden" name="lokasi" value="{{ request('lokasi') }}">
        @endif
    </form>

    {{-- Spacer --}}
    <div class="ms-auto d-flex align-items-center gap-2 flex-wrap">
        {{-- Filter Divisi — inline button group selalu tampil --}}
        @php
        $filterBtns = [
            ''             => ['label' => 'Semua Devisi', 'icon' => 'bi-grid-3x3-gap', 'active' => 'btn-secondary',  'inactive' => 'btn-outline-secondary', 'style' => ''],
            'gudang_utama' => ['label' => 'Gudang Utama', 'icon' => 'bi-building',      'active' => '',               'inactive' => '',                      'style' => 'biru'],
            'gudang_resto' => ['label' => 'Gudang Resto', 'icon' => 'bi-shop',          'active' => 'btn-success',    'inactive' => 'btn-outline-success',   'style' => ''],
            'kasir'        => ['label' => 'Kasir',        'icon' => 'bi-cash-coin',     'active' => '',               'inactive' => '',                      'style' => 'kasir'],
            'kitchen'      => ['label' => 'Kitchen',      'icon' => 'bi-egg-fried',     'active' => 'btn-danger',     'inactive' => 'btn-outline-danger',    'style' => ''],
        ];
        $currentLokasi = request('lokasi', '');
        @endphp
        <div class="d-flex flex-wrap gap-1">
            @foreach($filterBtns as $val => $cfg)
            @php
                $isActive = ($currentLokasi === $val);
                if ($cfg['style'] === 'biru') {
                    $btnClass    = 'btn-sm';
                    $inlineStyle = $isActive
                        ? 'font-size:.78rem;white-space:nowrap;background:#2563eb;border-color:#2563eb;color:#fff;'
                        : 'font-size:.78rem;white-space:nowrap;background:transparent;border-color:#2563eb;color:#2563eb;';
                } elseif ($cfg['style'] === 'kasir') {
                    $btnClass    = 'btn-sm';
                    $inlineStyle = $isActive
                        ? 'font-size:.78rem;white-space:nowrap;background:#f97316;border-color:#f97316;color:#fff;'
                        : 'font-size:.78rem;white-space:nowrap;background:transparent;border-color:#f97316;color:#f97316;';
                } else {
                    $btnClass    = 'btn-sm ' . ($isActive ? $cfg['active'] : $cfg['inactive']);
                    $inlineStyle = 'font-size:.78rem;white-space:nowrap;';
                }
                $href = route('admin.stock_in.riwayat', array_filter(['tanggal' => $tanggal->toDateString(), 'lokasi' => $val]));
            @endphp
            <a href="{{ $href }}"
               class="btn {{ $btnClass }} d-flex align-items-center gap-1"
               style="{{ $inlineStyle }}">
                <i class="bi {{ $cfg['icon'] }}"></i>
                <span>{{ $cfg['label'] }}</span>
            </a>
            @endforeach
        </div>

        {{-- Ikon Hapus (toggle mode seleksi) --}}
        <button type="button" class="btn btn-sm btn-outline-danger" id="btnToggleDelete" title="Mode Hapus">
            <i class="bi bi-trash3"></i>
        </button>
    </div>
</div>

{{-- Form hapus massal --}}
<form method="POST" id="formDelete" action="{{ route('admin.stock_in.destroy_bulk') }}">
    @csrf
    @method('DELETE')

    <div class="kk-stat-card p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0 text-center align-middle">
                <thead class="table-light">
                    <tr>
                        {{-- Kolom checkbox — hanya muncul di mode hapus --}}
                        <th class="text-center delete-col d-none" style="width:50px;">
                            <span style="font-size:.65rem; font-weight:700; color:#444; white-space:nowrap; display:block; margin-bottom:4px; text-transform:none;">Pilih Semua</span>
                            <input type="checkbox" id="checkAll" class="form-check-input d-block mx-auto">
                        </th>
                        <th class="text-center">No</th>
                        <th class="text-center">Nama Barang</th>
                        <th class="text-center">Jumlah</th>
                        <th class="text-center">Satuan</th>
                        <th class="text-center align-middle">Devisi</th>
                        <th class="text-center">Keterangan</th>
                        <th class="text-center">Dicatat Oleh</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($stockIns as $s)
                    @php
                        $lokasi      = $s->item->master_location ?? '';
                        $canDelete   = in_array($lokasi, $adminDeletable);
                    @endphp
                    <tr>
                        {{-- Checkbox hapus --}}
                        <td class="text-center delete-col d-none align-middle">
                            <input type="checkbox" name="ids[]" value="{{ $s->id }}" class="form-check-input row-check">
                        </td>
                        <td class="text-center"><strong>{{ $loop->iteration }}</strong></td>
                        <td class="text-center fw-bold" data-search="nama-barang">{{ $s->item->name }}</td>
                        <td class="text-center">{{ $s->quantity }}</td>
                        <td class="text-center">{{ $s->item->unit }}</td>
                        <td class="text-center">
                            <span class="badge" style="background:#bfdbfe;color:#1d4ed8;font-weight:600;">{{ $s->item->masterLocationLabel() }}</span>
                        </td>
                        <td class="text-center">
                            {{ $s->keterangan ?? '-' }}
                            @if($s->is_completed)
                                <i class="bi bi-check-circle-fill text-success ms-1" title="Selesai"></i>
                            @endif
                        </td>
                        <td class="text-center">
                            <span class="badge" style="background:#bbf7d0;color:#15803d;font-weight:600;">{{ $s->user->role?->name ?? '?' }}</span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="text-center text-muted py-3">Belum ada barang masuk pada tanggal ini.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Tombol konfirmasi hapus — muncul saat mode hapus aktif --}}
        <div id="deleteBar" class="d-none px-3 py-2 border-top d-flex align-items-center gap-3" style="background: var(--kk-card-bg);">
            <span class="small" style="color: var(--kk-text);"><strong id="selectedCount">0</strong> item dipilih</span>
            <button type="submit" class="btn btn-danger btn-sm" id="btnConfirmDelete" disabled
                    onclick="return confirm('Yakin hapus item yang dipilih?')">
                <i class="bi bi-trash3-fill"></i> Hapus Terpilih
            </button>
            <button type="button" class="btn btn-outline-secondary btn-sm" id="btnCancelDelete">
                Batal
            </button>
        </div>

        @if($stockIns->hasPages())
        <div class="p-3">{{ $stockIns->links() }}</div>
        @endif
    </div>
</form>

@push('scripts')
<script>
(function () {
    const btnToggle  = document.getElementById('btnToggleDelete');
    const btnCancel  = document.getElementById('btnCancelDelete');
    const deleteBar  = document.getElementById('deleteBar');
    const checkAll   = document.getElementById('checkAll');
    const deleteCols = document.querySelectorAll('.delete-col');
    const rowChecks  = document.querySelectorAll('.row-check');
    const countEl    = document.getElementById('selectedCount');
    const btnConfirm = document.getElementById('btnConfirmDelete');
    let deleteMode   = false;

    function updateCount() {
        const n = document.querySelectorAll('.row-check:checked').length;
        countEl.textContent = n;
        btnConfirm.disabled = n === 0;
    }

    function enterDeleteMode() {
        deleteMode = true;
        deleteCols.forEach(el => el.classList.remove('d-none'));
        deleteBar.classList.remove('d-none');
        btnToggle.classList.add('active');
    }

    function exitDeleteMode() {
        deleteMode = false;
        deleteCols.forEach(el => el.classList.add('d-none'));
        deleteBar.classList.add('d-none');
        btnToggle.classList.remove('active');
        if (checkAll) checkAll.checked = false;
        rowChecks.forEach(c => c.checked = false);
        updateCount();
    }

    btnToggle.addEventListener('click', () => deleteMode ? exitDeleteMode() : enterDeleteMode());
    if (btnCancel) btnCancel.addEventListener('click', exitDeleteMode);

    if (checkAll) {
        checkAll.addEventListener('change', () => {
            rowChecks.forEach(c => { if (!c.disabled) c.checked = checkAll.checked; });
            updateCount();
        });
    }

    rowChecks.forEach(c => c.addEventListener('change', updateCount));

    // ── Auto-submit filter tanggal (hanya event change, satu kali saat picker ditutup) ──
    const inputTanggal = document.getElementById('inputTanggal');
    const filterForm   = document.getElementById('filterForm');
    if (inputTanggal && filterForm) {
        inputTanggal.addEventListener('change', function () {
            if (this.value) filterForm.submit();
        });
    }
})();
</script>
@endpush

@endsection
