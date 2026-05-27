@extends('layouts.app')
@section('title', 'Suppliers')
@section('content')

<div class="card mb-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-2xl font-extrabold tracking-tight text-slate-800">Suppliers</h2>
            <p class="text-sm text-slate-400 mt-1">Manage supplier relationships and payment records</p>
        </div>
        <div class="flex items-center gap-3 flex-wrap">
            <div class="relative w-64">
                <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </span>
                <input id="supplier-search" type="text" placeholder="Search supplier..." class="form-input pl-10 w-full" />
            </div>
            <button onclick="openAddSupplier()" class="btn-primary flex items-center gap-1.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                <span>Add Supplier</span>
            </button>
        </div>
    </div>
</div>

<div class="card overflow-x-auto relative">
    <div id="suppliers-loading" class="absolute inset-0 bg-white/60 backdrop-blur-[2px] flex items-center justify-center z-10 rounded-xl">
        <div class="flex items-center gap-2">
            <svg class="animate-spin h-5 w-5 text-primary" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
            <span class="text-primary font-bold text-sm">Loading suppliers...</span>
        </div>
    </div>
    <table class="data-table">
        <thead>
            <tr><th>CODE</th><th>NAME</th><th>CONTACT</th><th>PHONE</th><th>CATEGORY</th><th>ACTIONS</th></tr>
        </thead>
        <tbody id="suppliers-tbody">
            <tr><td colspan="6" class="text-center text-text-secondary py-12">Loading suppliers...</td></tr>
        </tbody>
    </table>
</div>

{{-- Supplier Modal --}}
<div id="supplierModal" class="modal-overlay" onclick="if(event.target===this)closeModal('supplierModal')">
    <div class="modal-content">
        <form id="supplierForm" onsubmit="submitSupplier(event)">
            <input type="hidden" id="supplier_id" value="" />
            <div class="px-6 py-4 border-b border-border">
                <h3 id="supplier-modal-title" class="text-lg font-bold text-text-primary">Add Supplier</h3>
            </div>
            <div class="px-6 py-4 space-y-3">
                <div class="grid grid-cols-2 gap-3">
                    <div><label class="form-label">Code</label><input id="supplier_code" class="form-input" required /></div>
                    <div><label class="form-label">Name</label><input id="supplier_name" class="form-input" required /></div>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div><label class="form-label">Contact Person</label><input id="supplier_contact" class="form-input" /></div>
                    <div><label class="form-label">Phone</label><input id="supplier_phone" class="form-input" /></div>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div><label class="form-label">Category</label><input id="supplier_category" class="form-input" /></div>
                    <div><label class="form-label">Terms (Days)</label><input type="number" id="supplier_terms" class="form-input" value="30" /></div>
                </div>
            </div>
            <div class="px-6 py-4 border-t border-border flex justify-end gap-2">
                <button type="button" onclick="closeModal('supplierModal')" class="btn-ghost">Cancel</button>
                <button type="submit" id="supplier-submit-btn" class="btn-primary">Save</button>
            </div>
        </form>
    </div>
</div>

@endsection
@push('scripts')
<script>
    /**
     * SUPPLIERS PAGE — All data from GET /api/suppliers
     * CRUD via POST/PUT/DELETE /api/suppliers/*
     */
    let nextSupplierCode = 'S001';

    async function loadSuppliers(search = '') {
        document.getElementById('suppliers-loading').style.display = 'flex';
        const endpoint = '/suppliers' + (search ? `?search=${encodeURIComponent(search)}` : '');
        const data = await apiRequest('GET', endpoint);
        document.getElementById('suppliers-loading').style.display = 'none';
        if (!data) return;
        nextSupplierCode = data.next_code || 'S001';
        renderSuppliers(data.data || []);
    }

    function renderSuppliers(suppliers) {
        const tbody = document.getElementById('suppliers-tbody');
        if (!suppliers.length) {
            tbody.innerHTML = '<tr><td colspan="6" class="text-center text-text-secondary py-12">No suppliers found.</td></tr>';
            return;
        }
        tbody.innerHTML = suppliers.map(s => `<tr>
            <td><span class="inline-flex font-mono text-[11px] font-bold text-primary bg-primary/5 px-2.5 py-1 rounded-lg">${s.supplier_code}</span></td>
            <td class="font-bold text-slate-800">${s.name}</td>
            <td class="text-slate-600">${s.contact_person || '—'}</td>
            <td class="font-medium text-slate-600">${s.phone || '—'}</td>
            <td><span class="text-xs bg-slate-100 text-slate-600 px-2 py-1 rounded-md font-medium">${s.category || '—'}</span></td>
            <td>
                <div class="flex items-center gap-2">
                    <button onclick='openEditSupplier(${JSON.stringify(s)})' class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-slate-50 hover:bg-primary/10 hover:text-primary text-slate-600 rounded-lg text-xs font-semibold border border-slate-100 transition-colors cursor-pointer">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>Edit
                    </button>
                    <button onclick="deleteSupplier(${s.id}, '${s.name.replace(/'/g, "\\'")}')" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-rose-50/50 hover:bg-rose-500/10 hover:text-rose-600 text-rose-600 rounded-lg text-xs font-semibold border border-rose-100/50 transition-colors cursor-pointer">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>Delete
                    </button>
                </div>
            </td>
        </tr>`).join('');
    }

    function openAddSupplier() {
        document.getElementById('supplier_id').value = '';
        document.getElementById('supplier_code').value = nextSupplierCode;
        document.getElementById('supplier_name').value = '';
        document.getElementById('supplier_contact').value = '';
        document.getElementById('supplier_phone').value = '';
        document.getElementById('supplier_category').value = '';
        document.getElementById('supplier_terms').value = 30;
        document.getElementById('supplier-modal-title').textContent = 'Add Supplier';
        document.getElementById('supplier-submit-btn').textContent = 'Save Supplier';
        openModal('supplierModal');
    }

    function openEditSupplier(s) {
        document.getElementById('supplier_id').value = s.id;
        document.getElementById('supplier_code').value = s.supplier_code;
        document.getElementById('supplier_name').value = s.name;
        document.getElementById('supplier_contact').value = s.contact_person || '';
        document.getElementById('supplier_phone').value = s.phone || '';
        document.getElementById('supplier_category').value = s.category || '';
        document.getElementById('supplier_terms').value = s.terms_days || 30;
        document.getElementById('supplier-modal-title').textContent = 'Edit Supplier';
        document.getElementById('supplier-submit-btn').textContent = 'Update Supplier';
        openModal('supplierModal');
    }

    async function submitSupplier(e) {
        e.preventDefault();
        const id = document.getElementById('supplier_id').value;
        const isEdit = !!id;
        const btn = e.target.querySelector('[type=submit]');
        btn.disabled = true; btn.textContent = 'Saving...';
        const payload = {
            supplier_code: document.getElementById('supplier_code').value,
            name: document.getElementById('supplier_name').value,
            contact_person: document.getElementById('supplier_contact').value,
            phone: document.getElementById('supplier_phone').value,
            category: document.getElementById('supplier_category').value,
            terms_days: document.getElementById('supplier_terms').value,
        };
        const data = isEdit
            ? await apiRequest('PUT', '/suppliers/' + id, payload)
            : await apiRequest('POST', '/suppliers', payload);
        btn.disabled = false; btn.textContent = isEdit ? 'Update Supplier' : 'Save Supplier';
        if (data && data.success) {
            closeModal('supplierModal');
            showToast(isEdit ? 'Supplier updated.' : 'Supplier added.');
            loadSuppliers();
        }
    }

    async function deleteSupplier(id, name) {
        if (!confirm('Delete supplier "' + name + '"?')) return;
        const data = await apiRequest('DELETE', '/suppliers/' + id);
        if (data && data.success) {
            showToast('Supplier removed.');
            loadSuppliers();
        }
    }

    let searchTimer;
    document.addEventListener('DOMContentLoaded', () => {
        document.getElementById('supplier-search').addEventListener('input', function() {
            clearTimeout(searchTimer);
            searchTimer = setTimeout(() => loadSuppliers(this.value), 350);
        });

        loadSuppliers();
    });
</script>
@endpush
