@extends('layouts.app')
@section('title', 'Dashboard Kitchen')

@section('content')

{{-- Hero Greeting --}}
<div class="kk-hero-card mb-4 position-relative" id="heroBgContainer" onclick="document.getElementById('heroBgInput').click()" style="cursor: pointer; background: linear-gradient(135deg, #064e3b 0%, #1a1d2e 60%, #022c22 100%); background-size: cover; background-position: center; transition: background-image 0.3s ease-in-out;" title="Klik untuk mengganti gambar latar">
    {{-- Overlay to ensure text readability --}}
    <div style="position: absolute; inset: 0; background: linear-gradient(135deg, rgba(15,23,42,0.85) 0%, rgba(15,23,42,0.6) 100%); border-radius: inherit; z-index: 0; pointer-events: none;"></div>
    
    <div class="row align-items-center position-relative" style="z-index: 1;">
        <div class="col-md-12">
            <div class="kk-hero-greeting" style="color:rgba(110,231,183,0.7);">PANEL KITCHEN</div>
            <div class="kk-hero-name">Halo, {{ $user->name }}!</div>
            <div class="kk-hero-sub">Pantau stok bahan dan catat aktivitas dapur hari ini.</div>
            <div class="kk-hero-badge" style="background:rgba(16,185,129,0.2); border-color:rgba(16,185,129,0.3); color:#6ee7b7;">
                <i class="bi bi-calendar3"></i>
                {{ now()->translatedFormat('l, d F Y') }}
            </div>
        </div>
    </div>
</div>
<input type="file" class="d-none" id="heroBgInput" accept="image/*">

<style>
.kk-qa-card {
    display: flex !important;
    flex-direction: column !important;
    align-items: center;
    justify-content: center;
    text-align: center;
    gap: 10px;
    padding: 18px 10px;
    border-radius: 14px;
    border: 1px solid var(--kk-border, rgba(255,255,255,0.1));
    background: var(--kk-surface-2, rgba(255,255,255,0.04));
    text-decoration: none !important;
    transition: all 0.22s ease;
    width: 100%;
    min-height: 105px;
}
.kk-qa-card:hover {
    transform: translateY(-4px);
    border-color: var(--qa-accent, #6366f1);
    box-shadow: 0 8px 24px var(--qa-glow, rgba(99,102,241,0.3));
}
.kk-qa-icon {
    width: 46px; height: 46px;
    border-radius: 13px;
    display: flex; align-items: center; justify-content: center;
    font-size: 1.3rem;
    border: 1px solid;
    flex-shrink: 0;
    transition: transform 0.22s, box-shadow 0.22s;
    position: relative;
    overflow: hidden;
}
.kk-qa-icon::after {
    content: '';
    position: absolute;
    inset: 0;
    background: linear-gradient(135deg, rgba(255,255,255,0.22) 0%, transparent 60%);
    opacity: 0;
    transition: opacity 0.22s;
}
.kk-qa-card:hover .kk-qa-icon::after { opacity: 1; }
.kk-qa-card:hover .kk-qa-icon {
    transform: scale(1.15) rotate(-6deg);
    box-shadow: 0 6px 16px var(--qa-glow, rgba(99,102,241,0.4));
}
.kk-qa-label {
    display: block;
    font-size: 0.8rem;
    font-weight: 700;
    color: var(--kk-text, #1e293b);
    line-height: 1.2;
    margin-top: 2px;
}
.kk-qa-sub {
    display: block;
    font-size: 0.66rem;
    color: var(--kk-text-muted, #64748b);
    line-height: 1.1;
}
</style>

<div class="row g-3 mb-4">
    {{-- Aksi Cepat --}}
    <div class="col-12">
        <div class="kk-stat-card">
            <div class="kk-section-header mb-3">
                <div class="kk-section-title">
                    <i class="bi bi-lightning-charge" style="color:#facc15;"></i> Aksi Cepat
                </div>
            </div>
            <div class="row g-2">
                <div class="col-6 col-md-3">
                    <a href="{{ route('kitchen.orders.create') }}" class="kk-qa-card" style="--qa-accent:#f59e0b; --qa-glow:rgba(245,158,11,0.35);">
                        <div class="kk-qa-icon" style="background:linear-gradient(135deg,rgba(245,158,11,0.3),rgba(245,158,11,0.1)); color:#fbbf24; border-color:rgba(245,158,11,0.4);">
                            <i class="bi bi-plus-circle-fill"></i>
                        </div>
                        <span class="kk-qa-label">Buat Order</span>
                        <span class="kk-qa-sub">Ajukan permintaan</span>
                    </a>
                </div>
                <div class="col-6 col-md-3">
                    <a href="{{ route('kitchen.stock_in.index') }}" class="kk-qa-card" style="--qa-accent:#38bdf8; --qa-glow:rgba(56,189,248,0.35);">
                        <div class="kk-qa-icon" style="background:linear-gradient(135deg,rgba(56,189,248,0.3),rgba(56,189,248,0.1)); color:#38bdf8; border-color:rgba(56,189,248,0.4);">
                            <i class="bi bi-box-arrow-in-down-right"></i>
                        </div>
                        <span class="kk-qa-label">Barang Masuk</span>
                        <span class="kk-qa-sub">Input harian</span>
                    </a>
                </div>
                <div class="col-6 col-md-3">
                    <a href="{{ route('kitchen.stock_out.index') }}" class="kk-qa-card" style="--qa-accent:#f43f5e; --qa-glow:rgba(244,63,94,0.35);">
                        <div class="kk-qa-icon" style="background:linear-gradient(135deg,rgba(244,63,94,0.3),rgba(244,63,94,0.1)); color:#fb7185; border-color:rgba(244,63,94,0.4);">
                            <i class="bi bi-box-arrow-up-right"></i>
                        </div>
                        <span class="kk-qa-label">Barang Keluar</span>
                        <span class="kk-qa-sub">Stok keluar</span>
                    </a>
                </div>
                <div class="col-6 col-md-3">
                    <a href="{{ route('kitchen.stock_harian.index') }}" class="kk-qa-card" style="--qa-accent:#10b981; --qa-glow:rgba(16,185,129,0.35);">
                        <div class="kk-qa-icon" style="background:linear-gradient(135deg,rgba(16,185,129,0.3),rgba(16,185,129,0.1)); color:#34d399; border-color:rgba(16,185,129,0.4);">
                            <i class="bi bi-bar-chart-line-fill"></i>
                        </div>
                        <span class="kk-qa-label">Total Stock</span>
                        <span class="kk-qa-sub">Rekapitulasi</span>
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- Stok Rendah Kitchen --}}
    <div class="col-12">
        <div class="kk-stat-card">
            <div class="kk-section-header mb-3">
                <div class="kk-section-title text-danger">
                    <i class="bi bi-exclamation-triangle"></i> Peringatan Stok Rendah
                </div>
                <span class="badge bg-danger rounded-pill">{{ $stokRendah }}</span>
            </div>
            @if($stokRendah > 0)
                <div class="d-flex flex-column flex-md-row gap-3 flex-wrap">
                    @foreach(\App\Models\Item::where('master_location', \App\Models\Item::MASTER_KITCHEN)->whereHas('stockIns')->get()->filter(fn($i) => $i->stokKitchen() <= 10)->take(5) as $item)
                        <div class="d-flex align-items-center justify-content-between p-3 rounded flex-fill" style="background: var(--kk-danger-soft); border: 1px solid rgba(239, 68, 68, 0.2);">
                            <div>
                                <div class="fw-semibold" style="font-size:0.95rem; color:var(--kk-text);">{{ $item->name }}</div>
                                <div style="font-size:0.85rem; color:var(--kk-danger);">Sisa: {{ $item->stokKitchen() }} {{ $item->unit }}</div>
                            </div>
                            <a href="{{ route('kitchen.orders.create', ['item_id' => $item->id]) }}" class="btn btn-sm btn-outline-danger" style="font-weight: 500;">Restock</a>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-4 text-muted" style="font-size:0.95rem;">
                    <i class="bi bi-check-circle text-success fs-2 d-block mb-2"></i>
                    Stok kitchen aman.
                </div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const heroBgContainer = document.getElementById('heroBgContainer');
    const heroBgInput = document.getElementById('heroBgInput');
    
    const savedBg = localStorage.getItem('heroBgImage');
    if (savedBg) {
        heroBgContainer.style.backgroundImage = `url(${savedBg})`;
    }

    if (heroBgInput) {
        heroBgInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(event) {
                    const base64Image = event.target.result;
                    heroBgContainer.style.backgroundImage = `url(${base64Image})`;
                    try {
                        localStorage.setItem('heroBgImage', base64Image);
                    } catch (err) {
                        console.error("File terlalu besar untuk localStorage", err);
                        alert("Gambar ini terlalu besar untuk disimpan di memori browser. Gambar akan hilang saat direfresh.");
                    }
                };
                reader.readAsDataURL(file);
            }
        });
    }
});
</script>
@endpush

@endsection
