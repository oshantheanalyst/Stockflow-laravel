@extends('layouts.app')
@section('title', 'Reminders')
@section('content')

<div class="card mb-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-2xl font-extrabold tracking-tight text-slate-800">Reminders</h2>
            <p class="text-sm text-slate-400 mt-1">Unpaid invoices and pending supplier payments</p>
        </div>
        <div class="flex items-center gap-2">
            <span id="overdue-badge" class="text-xs font-bold bg-rose-500/10 text-rose-600 border border-rose-500/20 px-3 py-1.5 rounded-xl" style="display:none;">
                <span id="overdue-count">0</span> Overdue
            </span>
            <button onclick="loadReminders()" class="btn-ghost flex items-center gap-1.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                Refresh
            </button>
        </div>
    </div>
</div>

<div class="card overflow-x-auto relative">
    <div id="reminders-loading" class="absolute inset-0 bg-white/60 backdrop-blur-[2px] flex items-center justify-center z-10 rounded-xl">
        <div class="flex items-center gap-2">
            <svg class="animate-spin h-5 w-5 text-primary" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
            <span class="text-primary font-bold text-sm">Loading reminders...</span>
        </div>
    </div>
    <table class="data-table">
        <thead>
            <tr><th>TYPE</th><th>REFERENCE</th><th>NAME</th><th>AMOUNT</th><th>DUE DATE</th><th>STATUS</th><th>ACTIONS</th></tr>
        </thead>
        <tbody id="reminders-tbody">
            <tr><td colspan="7" class="text-center text-text-secondary py-12">Loading reminders...</td></tr>
        </tbody>
    </table>
</div>

@endsection
@push('scripts')
<script>
    /**
     * REMINDERS PAGE — Data from GET /api/reminders
     * Mark complete via PUT /api/reminders/{id}
     */
    async function loadReminders() {
        document.getElementById('reminders-loading').style.display = 'flex';
        const data = await apiRequest('GET', '/reminders');
        document.getElementById('reminders-loading').style.display = 'none';
        if (!data) return;

        const overdueCount = data.overdue_count || 0;
        if (overdueCount > 0) {
            document.getElementById('overdue-count').textContent = overdueCount;
            document.getElementById('overdue-badge').style.display = '';
        }
        renderReminders(data.data || []);
    }

    function renderReminders(reminders) {
        const tbody = document.getElementById('reminders-tbody');
        if (!reminders.length) {
            tbody.innerHTML = '<tr><td colspan="7" class="text-center text-text-secondary py-12">No pending reminders. All payments are up to date! ✅</td></tr>';
            return;
        }
        tbody.innerHTML = reminders.map(r => {
            const isOverdue = r.status === 'Overdue';
            const statusBadge = isOverdue
                ? '<span class="badge-danger"><span class="w-1.5 h-1.5 rounded-full bg-rose-500 animate-pulse"></span> Overdue</span>'
                : '<span class="badge-warning">Pending</span>';
            const typeBadge = r.type === 'Invoice'
                ? '<span class="text-xs bg-indigo-100 text-indigo-700 px-2 py-1 rounded-md font-semibold">Invoice</span>'
                : '<span class="text-xs bg-amber-100 text-amber-700 px-2 py-1 rounded-md font-semibold">Supplier</span>';
            const dueDate = r.due_date ? new Date(r.due_date).toLocaleDateString('en-GB', { day: '2-digit', month: 'short', year: 'numeric' }) : '—';

            return `<tr class="${isOverdue ? 'bg-rose-50/30' : ''}">
                <td>${typeBadge}</td>
                <td class="font-mono text-xs font-bold">${r.reference || '—'}</td>
                <td class="font-semibold text-slate-700">${r.name}</td>
                <td class="font-bold ${isOverdue ? 'text-rose-600' : 'text-slate-800'}">${formatCurrency(r.amount)}</td>
                <td class="${isOverdue ? 'text-rose-600 font-semibold' : ''}">${dueDate}</td>
                <td>${statusBadge}</td>
                <td>
                    <button onclick="markComplete(${r.id}, '${r.type}')" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 rounded-lg text-xs font-semibold border border-emerald-100 transition-colors cursor-pointer">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                        Mark Paid
                    </button>
                </td>
            </tr>`;
        }).join('');
    }

    async function markComplete(id, type) {
        if (!confirm('Mark this ' + type + ' payment as complete?')) return;
        const data = await apiRequest('PUT', '/reminders/' + id, { type });
        if (data && data.success) {
            showToast('Marked as paid successfully.');
            loadReminders();
        }
    }

    document.addEventListener('DOMContentLoaded', () => {
        loadReminders();
    });
</script>
@endpush
