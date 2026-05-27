@extends('layouts.app')
@section('title', 'Sales & Invoices')
@section('content')

<div class="card mb-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-2xl font-extrabold tracking-tight text-slate-800">Sales & Invoices</h2>
            <p class="text-sm text-slate-400 mt-1">Manage customer invoices and sales</p>
        </div>
        <div class="flex items-center gap-2 flex-wrap">
            <div class="relative w-64">
                <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </span>
                <input id="sales-search" type="text" placeholder="Search invoice or customer..." class="form-input pl-10 w-64" />
            </div>
            <a href="/sales/create" class="btn-primary flex items-center gap-1.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                <span>+ New Sale</span>
            </a>
        </div>
    </div>
</div>

<div class="card overflow-x-auto relative">
    <div id="sales-loading" class="absolute inset-0 bg-white/60 backdrop-blur-[2px] flex items-center justify-center z-10 rounded-xl">
        <div class="flex items-center gap-2">
            <svg class="animate-spin h-5 w-5 text-primary" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
            <span class="text-primary font-bold text-sm">Loading invoices...</span>
        </div>
    </div>
    <table class="data-table">
        <thead>
            <tr>
                <th>INV NO</th><th>DATE</th><th>CUSTOMER</th><th>PAYMENT</th><th>TOTAL</th><th>STATUS</th>
                <th id="sales-actions-col" style="display:none;">ACTIONS</th>
            </tr>
        </thead>
        <tbody id="sales-tbody">
            <tr><td colspan="7" class="text-center text-text-secondary py-12">Loading invoices...</td></tr>
        </tbody>
    </table>
</div>

@endsection
@push('scripts')
<script>
    /**
     * SALES PAGE — All data from GET /api/sales
     * Delete via DELETE /api/sales/{id}
     */
    let user = null;
    let canDelete = false;

    document.addEventListener('DOMContentLoaded', () => {
        user = getCurrentUser();
        canDelete = user && user.role === 'Admin';
        if (canDelete) document.getElementById('sales-actions-col').style.display = '';
        
        document.getElementById('sales-search').addEventListener('input', function() {
            clearTimeout(searchTimer);
            searchTimer = setTimeout(() => loadSales(this.value), 350);
        });

        loadSales();
    });

    async function loadSales(search = '') {
        document.getElementById('sales-loading').style.display = 'flex';
        const endpoint = '/sales' + (search ? `?search=${encodeURIComponent(search)}` : '');
        const data = await apiRequest('GET', endpoint);
        document.getElementById('sales-loading').style.display = 'none';
        if (!data) return;
        renderSales(data.data || []);
    }

    function renderSales(invoices) {
        const tbody = document.getElementById('sales-tbody');
        if (!invoices.length) {
            tbody.innerHTML = '<tr><td colspan="7" class="text-center text-text-secondary py-8">No invoices found.</td></tr>';
            return;
        }
        tbody.innerHTML = invoices.map(inv => {
            const statusBadge = inv.is_paid
                ? '<span class="badge-success"><span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Paid</span>'
                : '<span class="badge-warning">Pending</span>';
            const date = inv.invoice_date ? new Date(inv.invoice_date).toLocaleDateString('en-GB', { day: '2-digit', month: 'short', year: 'numeric' }) : '—';
            const deleteBtn = canDelete ? `<td><button onclick="deleteSale(${inv.id}, '${inv.invoice_no}')" class="text-rose-500 hover:underline text-xs font-semibold cursor-pointer">🗑 Delete</button></td>` : '';
            return `<tr>
                <td class="font-mono text-xs font-medium">${inv.invoice_no}</td>
                <td>${date}</td>
                <td>${inv.customer ? inv.customer.name : 'Walk-in'}</td>
                <td>${inv.payment_method}</td>
                <td class="font-semibold">${formatCurrency(inv.total)}</td>
                <td>${statusBadge}</td>
                ${deleteBtn}
            </tr>`;
        }).join('');
    }

    async function deleteSale(id, invoiceNo) {
        if (!confirm('Delete invoice ' + invoiceNo + '?')) return;
        const data = await apiRequest('DELETE', '/sales/' + id);
        if (data && data.success) {
            showToast('Invoice deleted.');
            loadSales();
        }
    }

    let searchTimer;
</script>
@endpush
