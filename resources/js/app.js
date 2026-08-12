import './bootstrap';

import 'bootstrap/dist/css/bootstrap.min.css';
import 'bootstrap-icons/font/bootstrap-icons.css';
import * as bootstrap from 'bootstrap';
import Chart from 'chart.js/auto';

// Expose ke window supaya bisa dipakai dari script inline di blade bila perlu
window.bootstrap = bootstrap;
window.Chart = Chart;

// ==========================================================================
// Pencarian Nama Barang — berlaku otomatis di semua tabel stock divisi
// (Gudang Utama, Gudang Resto, Kasir, Kitchen, Riwayat Barang Masuk).
// Cukup taruh input dengan class "kk-search-nama-barang" di halaman mana pun,
// dan taruh data-search="nama-barang" di <td> nama barangnya.
// ==========================================================================
document.addEventListener('input', function (e) {
    if (!e.target.classList.contains('kk-search-nama-barang')) return;

    const keyword = e.target.value.trim().toLowerCase();
    const card = e.target.closest('.kk-stat-card') || document;
    const table = card.querySelector('table');
    if (!table) return;

    const rows = table.querySelectorAll('tbody tr');
    let visibleCount = 0;

    rows.forEach(function (row) {
        if (row.classList.contains('kk-search-empty-row')) return;
        const cell = row.querySelector('td[data-search="nama-barang"]');
        if (!cell) { return; }
        const match = cell.textContent.toLowerCase().includes(keyword);
        row.style.display = match ? '' : 'none';
        if (match) visibleCount++;
    });

    let emptyRow = table.querySelector('.kk-search-empty-row');
    if (keyword && visibleCount === 0) {
        if (!emptyRow) {
            const colCount = table.querySelectorAll('thead th').length || 1;
            emptyRow = document.createElement('tr');
            emptyRow.className = 'kk-search-empty-row';
            emptyRow.innerHTML = '<td colspan="' + colCount + '" class="text-center text-muted py-3">Barang tidak ditemukan.</td>';
            table.querySelector('tbody').appendChild(emptyRow);
        }
    } else if (emptyRow) {
        emptyRow.remove();
    }
});
