{{--
    Partial reusable dipakai oleh 4 tab lokasi (Gudang/Resto/Kasir/Kitchen)
    di admin/stock/index.blade.php.

    Variabel yang harus dikirim:
    - $categories : koleksi kategori (sudah difilter & di-values() oleh pemanggil)
    - $locationKey: 'gudang' | 'resto' | 'kasir' | 'kitchen'
    - $prefix     : string unik untuk id HTML tab (hindari bentrok antar 4 lokasi)
--}}
<ul class="nav nav-tabs mb-3">
    @foreach($categories as $i => $cat)
    <li class="nav-item">
        <a class="nav-link {{ $i === 0 ? 'active' : '' }}" data-bs-toggle="tab" href="#{{ $prefix }}-cat{{ $cat->id }}">
            {{ $cat->name }}
            @if($cat->used_by === 'kasir')
                <span class="badge bg-info text-dark ms-1">Kasir</span>
            @elseif($cat->used_by === 'kitchen')
                <span class="badge bg-warning text-dark ms-1">Kitchen</span>
            @else
                <span class="badge bg-secondary ms-1">Umum</span>
            @endif
        </a>
    </li>
    @endforeach
</ul>

<div class="tab-content">
    @forelse($categories as $i => $cat)
    <div class="tab-pane fade {{ $i === 0 ? 'show active' : '' }}" id="{{ $prefix }}-cat{{ $cat->id }}">
        <div class="table-responsive">
            <table class="table table-sm table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Barang</th>
                        <th>Satuan</th>
                        <th class="text-end">Stok</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($cat->items as $item)
                    @php $stok = $item->stokByLocation($locationKey); @endphp
                    <tr>
                        <td>{{ $item->name }}</td>
                        <td>{{ $item->unit }}</td>
                        <td class="text-end">
                            <span class="badge {{ $stok <= $item->min_stock ? 'bg-danger' : 'bg-primary' }}">
                                {{ $stok }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="3" class="text-center text-muted py-3">Belum ada barang di kategori ini.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @empty
    <p class="text-muted mb-0">Belum ada kategori yang relevan untuk bagian ini.</p>
    @endforelse
</div>
