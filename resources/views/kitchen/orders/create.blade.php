@extends('layouts.app')
@section('title', 'Buat Permintaan Barang')
@section('content')

<div class="kk-stat-card">
    <h5 class="mb-3">Orderan Barang Harian Kitchen</h5>
    <form action="{{ route('kitchen.orders.store') }}" method="POST" id="form-order">
        @csrf
        <div class="table-responsive border rounded-3 mb-3">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="text-center" style="width: 50px;">No</th>
                        <th class="text-center">Nama Barang <span class="text-danger">*</span></th>
                        <th class="text-center" style="width: 150px;">Jumlah <span class="text-danger">*</span></th>
                        <th class="text-center">Keterangan</th>
                        <th class="text-center" style="width: 80px;"><i class="bi bi-gear"></i></th>
                    </tr>
                </thead>
                <tbody id="order-items-tbody">
                    <tr class="item-row">
                        <td class="text-center fw-bold row-number">1</td>
                        <td>
                            <input type="text" name="items[0][item_name]" class="form-control item-input" required placeholder="Ketik nama barang..." autocomplete="off">
                        </td>
                        <td>
                            <input type="number" name="items[0][quantity]" class="form-control text-center quantity-input" value="1" min="1" required>
                        </td>
                        <td>
                            <input type="text" name="items[0][keterangan]" class="form-control" placeholder="Keterangan tambahan...">
                        </td>
                        <td class="text-center">
                            <button type="button" class="btn btn-sm btn-outline-danger btn-remove-row" disabled><i class="bi bi-trash"></i></button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        
        
        
        <div class="d-flex justify-content-between align-items-center mb-3">
            <button type="button" class="btn btn-sm btn-outline-primary fw-semibold" id="btn-add-row" style="border-radius: 8px;">
                <i class="bi bi-plus-circle me-1"></i> Tambah Baris
            </button>
        </div>

        
        <div class="d-flex justify-content-end">
            <button type="submit" class="btn text-white fw-semibold px-4" style="background-color: #0d6efd !important; border-color: #0d6efd !important; border-radius: 10px;">Kirim Permintaan</button>
        </div>
    </form>
</div>

{{-- Template for new row --}}
<template id="row-template">
    <tr class="item-row">
        <td class="text-center fw-bold row-number"></td>
        <td>
            <input type="text" name="items[INDEX][item_name]" class="form-control item-input" required placeholder="Ketik nama barang..." autocomplete="off">
        </td>
        <td>
            <input type="number" name="items[INDEX][quantity]" class="form-control text-center quantity-input" value="1" min="1" required>
        </td>
        <td>
            <input type="text" name="items[INDEX][keterangan]" class="form-control" placeholder="Keterangan tambahan...">
        </td>
        <td class="text-center">
            <button type="button" class="btn btn-sm btn-outline-danger btn-remove-row"><i class="bi bi-trash"></i></button>
        </td>
    </tr>
</template>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const tbody = document.getElementById('order-items-tbody');
        const btnAddRow = document.getElementById('btn-add-row');
        const template = document.getElementById('row-template');
        const formOrder = document.getElementById('form-order');
        let rowCount = 1;

        // --- DRAFT SAVING LOGIC ---
        const DRAFT_KEY = 'orderDraft_kitchen';

        function saveFormState() {
            const rows = tbody.querySelectorAll('.item-row');
            const draftData = [];
            rows.forEach((row) => {
                const nameInput = row.querySelector('.item-input');
                const qtyInput = row.querySelector('.quantity-input');
                const noteInput = row.querySelector('input[placeholder="Keterangan tambahan..."]');
                
                draftData.push({
                    name: nameInput ? nameInput.value : '',
                    qty: qtyInput ? qtyInput.value : '1',
                    note: noteInput ? noteInput.value : ''
                });
            });
            localStorage.setItem(DRAFT_KEY, JSON.stringify(draftData));
        }

        function loadFormState() {
            const draft = localStorage.getItem(DRAFT_KEY);
            if (draft) {
                try {
                    const draftData = JSON.parse(draft);
                    if (Array.isArray(draftData) && draftData.length > 0) {
                        // Clear existing rows
                        tbody.innerHTML = ''; 
                        
                        draftData.forEach((data) => {
                            const clone = template.content.cloneNode(true);
                            tbody.appendChild(clone);
                        });
                        
                        // Fill data
                        const rows = tbody.querySelectorAll('.item-row');
                        rows.forEach((row, idx) => {
                            if (draftData[idx]) {
                                row.querySelector('.item-input').value = draftData[idx].name || '';
                                row.querySelector('.quantity-input').value = draftData[idx].qty || '1';
                                row.querySelector('input[placeholder="Keterangan tambahan..."]').value = draftData[idx].note || '';
                            }
                        });
                        updateRowNumbers();
                    }
                } catch(e) {
                    console.error('Error loading draft', e);
                }
            }
        }

        // Listen for input changes to save draft
        tbody.addEventListener('input', function() {
            saveFormState();
        });

        // Clear draft on successful submit
        formOrder.addEventListener('submit', function() {
            localStorage.removeItem(DRAFT_KEY);
        });
        // --- END DRAFT SAVING LOGIC ---

        function updateRowNumbers() {
            const rows = tbody.querySelectorAll('.item-row');
            rows.forEach((row, index) => {
                row.querySelector('.row-number').textContent = index + 1;
                
                // Update names to have correct index for array submission
                row.querySelector('.item-input').name = `items[${index}][item_name]`;
                row.querySelector('.quantity-input').name = `items[${index}][quantity]`;
                row.querySelector('input[placeholder="Keterangan tambahan..."]').name = `items[${index}][keterangan]`;

                // Enable/disable remove button (don't allow removing if only 1 row left)
                const btnRemove = row.querySelector('.btn-remove-row');
                if (rows.length === 1) {
                    btnRemove.setAttribute('disabled', 'disabled');
                } else {
                    btnRemove.removeAttribute('disabled');
                }
            });
            rowCount = rows.length;
            saveFormState();
        }

        btnAddRow.addEventListener('click', function() {
            const clone = template.content.cloneNode(true);
            tbody.appendChild(clone);
            updateRowNumbers();
        });

        tbody.addEventListener('click', function(e) {
            const btn = e.target.closest('.btn-remove-row');
            if (btn && !btn.hasAttribute('disabled')) {
                btn.closest('tr').remove();
                updateRowNumbers();
            }
        });

        // Initialize form with draft if exists
        loadFormState();
    });
</script>
@endpush
