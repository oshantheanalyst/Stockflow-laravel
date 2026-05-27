@extends('layouts.app')
@section('title', 'Customers')
@section('content')

<div class="card mb-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-2xl font-extrabold tracking-tight text-slate-800">Customers</h2>
            <p class="text-sm text-slate-400 mt-1">Manage and track customer information and purchasing value</p>
        </div>
        <div class="flex items-center gap-3 flex-wrap">
            <div class="relative w-64">
                <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </span>
                <input id="customer-search" type="text" placeholder="Search customer..." class="form-input pl-10 w-full" />
            </div>
            <button onclick="openAddCustomer()" class="btn-primary flex items-center gap-1.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                <span>Add Customer</span>
            </button>
        </div>
    </div>
</div>

<div class="card overflow-x-auto relative">
    <div id="customers-loading" class="absolute inset-0 bg-white/60 backdrop-blur-[2px] flex items-center justify-center z-10 rounded-xl">
        <div class="flex items-center gap-2">
            <svg class="animate-spin h-5 w-5 text-primary" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
            <span class="text-primary font-bold text-sm">Loading customers...</span>
        </div>
    </div>
    <table class="data-table">
        <thead>
            <tr>
                <th>CODE</th><th>NAME</th><th>PHONE</th><th>AREA</th><th>ADDRESS</th>
                <th id="total-bought-col" style="display:none;">TOTAL BOUGHT</th>
                <th>ACTIONS</th>
            </tr>
        </thead>
        <tbody id="customers-tbody">
            <tr><td colspan="7" class="text-center text-text-secondary py-12">Loading customers...</td></tr>
        </tbody>
    </table>
</div>

{{-- Customer Modal --}}
<div id="customerModal" class="modal-overlay" onclick="if(event.target===this)closeModal('customerModal')">
    <div class="modal-content">
        <form id="customerForm" onsubmit="submitCustomer(event)">
            <input type="hidden" id="customer_id" value="" />
            <div class="px-6 py-4 border-b border-border">
                <h3 id="customer-modal-title" class="text-lg font-bold text-text-primary">Add Customer</h3>
            </div>
            <div class="px-6 py-4 space-y-3">
                <div class="grid grid-cols-2 gap-3">
                    <div><label class="form-label">Code</label><input id="customer_code" name="customer_code" class="form-input" required /></div>
                    <div><label class="form-label">Name</label><input id="customer_name" name="name" class="form-input" required /></div>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div><label class="form-label">Phone</label><input id="customer_phone" name="phone" class="form-input" /></div>
                    <div><label class="form-label">Area</label><input id="customer_area" name="area" class="form-input" /></div>
                </div>
                <div><label class="form-label">Address</label><input id="customer_address" name="address" class="form-input" /></div>
                <div><label class="form-label">Notes</label><textarea id="customer_notes" class="form-input" rows="2"></textarea></div>
            </div>
            <div class="px-6 py-4 border-t border-border flex justify-end gap-2">
                <button type="button" onclick="closeModal('customerModal')" class="btn-ghost">Cancel</button>
                <button type="submit" id="customer-submit-btn" class="btn-primary">Save</button>
            </div>
        </form>
    </div>
</div>

@endsection
@push('scripts')
<script>
    /**
     * CUSTOMERS PAGE — All data from GET /api/customers
     * CRUD via POST/PUT/DELETE /api/customers/*
     */
    let user = null;
    let isAdmin = false;
    let customerTotals = {};
    let nextCustomerCode = 'C001';

    document.addEventListener('DOMContentLoaded', () => {
        user = getCurrentUser();
        isAdmin = user && user.role === 'Admin';
        if (isAdmin && document.getElementById('total-bought-col')) {
            document.getElementById('total-bought-col').style.display = '';
        }
        
        document.getElementById('customer-search').addEventListener('input', function() {
            clearTimeout(searchTimer);
            searchTimer = setTimeout(() => loadCustomers(this.value), 350);
        });

        loadCustomers();
    });

    async function loadCustomers(search = '') {
        document.getElementById('customers-loading').style.display = 'flex';
        const endpoint = '/customers' + (search ? `?search=${encodeURIComponent(search)}` : '');
        const data = await apiRequest('GET', endpoint);
        document.getElementById('customers-loading').style.display = 'none';
        if (!data) return;

        customerTotals = data.totals || {};
        nextCustomerCode = data.next_code || 'C001';
        renderCustomers(data.data || []);
    }

    function renderCustomers(customers) {
        const tbody = document.getElementById('customers-tbody');
        if (!customers.length) {
            tbody.innerHTML = '<tr><td colspan="7" class="text-center text-text-secondary py-12">No customers found.</td></tr>';
            return;
        }
        tbody.innerHTML = customers.map(c => {
            const totalBought = isAdmin ? `<td class="font-bold text-indigo-600">${formatCurrency(customerTotals[c.id] || 0)}</td>` : '';
            return `<tr>
                <td><span class="inline-flex font-mono text-[11px] font-bold text-primary bg-primary/5 px-2.5 py-1 rounded-lg">${c.customer_code}</span></td>
                <td class="font-bold text-slate-800">${c.name}</td>
                <td class="font-medium text-slate-600">${c.phone || '—'}</td>
                <td><span class="text-xs bg-slate-100 text-slate-600 px-2.5 py-1 rounded-md font-medium">${c.area || '—'}</span></td>
                <td class="text-slate-500 text-xs max-w-xs truncate">${c.address || '—'}</td>
                ${totalBought}
                <td>
                    <div class="flex items-center gap-2">
                        <button onclick='openEditCustomer(${JSON.stringify(c)})' class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-slate-50 hover:bg-primary/10 hover:text-primary text-slate-600 rounded-lg text-xs font-semibold border border-slate-100 transition-colors cursor-pointer">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                            Edit
                        </button>
                        <button onclick="deleteCustomer(${c.id}, '${c.name.replace(/'/g, "\\'")}')" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-rose-50/50 hover:bg-rose-500/10 hover:text-rose-600 text-rose-600 rounded-lg text-xs font-semibold border border-rose-100/50 transition-colors cursor-pointer">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            Delete
                        </button>
                    </div>
                </td>
            </tr>`;
        }).join('');
    }

    function openAddCustomer() {
        document.getElementById('customer_id').value = '';
        document.getElementById('customer_code').value = nextCustomerCode;
        document.getElementById('customer_name').value = '';
        document.getElementById('customer_phone').value = '';
        document.getElementById('customer_area').value = '';
        document.getElementById('customer_address').value = '';
        document.getElementById('customer_notes').value = '';
        document.getElementById('customer-modal-title').textContent = 'Add Customer';
        document.getElementById('customer-submit-btn').textContent = 'Save Customer';
        openModal('customerModal');
    }

    function openEditCustomer(c) {
        document.getElementById('customer_id').value = c.id;
        document.getElementById('customer_code').value = c.customer_code;
        document.getElementById('customer_name').value = c.name;
        document.getElementById('customer_phone').value = c.phone || '';
        document.getElementById('customer_area').value = c.area || '';
        document.getElementById('customer_address').value = c.address || '';
        document.getElementById('customer_notes').value = c.notes || '';
        document.getElementById('customer-modal-title').textContent = 'Edit Customer';
        document.getElementById('customer-submit-btn').textContent = 'Update Customer';
        openModal('customerModal');
    }

    async function submitCustomer(e) {
        e.preventDefault();
        const id = document.getElementById('customer_id').value;
        const isEdit = !!id;
        const btn = e.target.querySelector('[type=submit]');
        btn.disabled = true; btn.textContent = 'Saving...';
        const payload = {
            customer_code: document.getElementById('customer_code').value,
            name: document.getElementById('customer_name').value,
            phone: document.getElementById('customer_phone').value,
            area: document.getElementById('customer_area').value,
            address: document.getElementById('customer_address').value,
            notes: document.getElementById('customer_notes').value,
        };
        const data = isEdit
            ? await apiRequest('PUT', '/customers/' + id, payload)
            : await apiRequest('POST', '/customers', payload);
        btn.disabled = false; btn.textContent = isEdit ? 'Update Customer' : 'Save Customer';
        if (data && data.success) {
            closeModal('customerModal');
            showToast(isEdit ? 'Customer updated.' : 'Customer added.');
            loadCustomers();
        }
    }

    async function deleteCustomer(id, name) {
        if (!confirm('Delete customer "' + name + '"?')) return;
        const data = await apiRequest('DELETE', '/customers/' + id);
        if (data && data.success) {
            showToast('Customer removed.');
            loadCustomers();
        }
    }

    let searchTimer;
</script>
@endpush
