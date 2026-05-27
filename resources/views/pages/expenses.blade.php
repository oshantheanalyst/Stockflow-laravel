@extends('layouts.app')
@section('title', 'Expenses')
@section('content')

<div class="card mb-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-2xl font-extrabold tracking-tight text-slate-800">Expenses</h2>
            <p class="text-sm text-slate-400 mt-1">Track and manage all business expenses</p>
        </div>
        <div class="flex items-center gap-3 flex-wrap">
            <div class="relative w-64">
                <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </span>
                <input id="expense-search" type="text" placeholder="Search category or description..." class="form-input pl-10 w-full" />
            </div>
            <button onclick="openAddExpense()" class="btn-primary flex items-center gap-1.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                <span>Add Expense</span>
            </button>
        </div>
    </div>
</div>

{{-- Stats --}}
<div class="grid grid-cols-1 sm:grid-cols-3 gap-5 mb-6">
    <div class="stat-card">
        <span class="text-[10px] font-extrabold text-slate-400 uppercase tracking-wider">Total Expenses</span>
        <div class="mt-2"><span id="stat-expense-total" class="text-2xl font-extrabold text-slate-800">—</span></div>
    </div>
    <div class="stat-card">
        <span class="text-[10px] font-extrabold text-slate-400 uppercase tracking-wider">Expense Count</span>
        <div class="mt-2"><span id="stat-expense-count" class="text-2xl font-extrabold text-slate-800">—</span></div>
    </div>
    <div class="stat-card">
        <span class="text-[10px] font-extrabold text-slate-400 uppercase tracking-wider">Average per Expense</span>
        <div class="mt-2"><span id="stat-expense-avg" class="text-2xl font-extrabold text-slate-800">—</span></div>
    </div>
</div>

<div class="card overflow-x-auto relative">
    <div id="expenses-loading" class="absolute inset-0 bg-white/60 backdrop-blur-[2px] flex items-center justify-center z-10 rounded-xl">
        <div class="flex items-center gap-2">
            <svg class="animate-spin h-5 w-5 text-primary" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
            <span class="text-primary font-bold text-sm">Loading expenses...</span>
        </div>
    </div>
    <table class="data-table">
        <thead>
            <tr><th>DATE</th><th>CATEGORY</th><th>DESCRIPTION</th><th>AMOUNT</th><th>ACTIONS</th></tr>
        </thead>
        <tbody id="expenses-tbody">
            <tr><td colspan="5" class="text-center text-text-secondary py-12">Loading expenses...</td></tr>
        </tbody>
    </table>
</div>

{{-- Expense Modal --}}
<div id="expenseModal" class="modal-overlay" onclick="if(event.target===this)closeModal('expenseModal')">
    <div class="modal-content">
        <form id="expenseForm" onsubmit="submitExpense(event)">
            <input type="hidden" id="expense_id" value="" />
            <div class="px-6 py-4 border-b border-border">
                <h3 id="expense-modal-title" class="text-lg font-bold text-text-primary">Add Expense</h3>
            </div>
            <div class="px-6 py-4 space-y-3">
                <div class="grid grid-cols-2 gap-3">
                    <div><label class="form-label">Date</label><input type="date" id="expense_date" class="form-input" required /></div>
                    <div><label class="form-label">Category</label><input id="expense_category" class="form-input" required /></div>
                </div>
                <div><label class="form-label">Description</label><input id="expense_description" class="form-input" /></div>
                <div><label class="form-label">Amount (Rs)</label><input type="number" step="0.01" id="expense_amount" class="form-input" required /></div>
            </div>
            <div class="px-6 py-4 border-t border-border flex justify-end gap-2">
                <button type="button" onclick="closeModal('expenseModal')" class="btn-ghost">Cancel</button>
                <button type="submit" id="expense-submit-btn" class="btn-primary">Save Expense</button>
            </div>
        </form>
    </div>
</div>

@endsection
@push('scripts')
<script>
    /**
     * EXPENSES PAGE — All data from GET /api/expenses
     * CRUD via POST/PUT/DELETE /api/expenses/*
     */
    async function loadExpenses(search = '') {
        document.getElementById('expenses-loading').style.display = 'flex';
        const endpoint = '/expenses' + (search ? `?search=${encodeURIComponent(search)}` : '');
        const data = await apiRequest('GET', endpoint);
        document.getElementById('expenses-loading').style.display = 'none';
        if (!data) return;

        const meta = data.meta || {};
        document.getElementById('stat-expense-total').textContent = formatCurrency(meta.total_expenses_val || 0);
        document.getElementById('stat-expense-count').textContent = (meta.expense_count || 0).toLocaleString();
        document.getElementById('stat-expense-avg').textContent = formatCurrency(meta.average_expense_val || 0);
        renderExpenses(data.data || []);
    }

    function renderExpenses(expenses) {
        const tbody = document.getElementById('expenses-tbody');
        if (!expenses.length) {
            tbody.innerHTML = '<tr><td colspan="5" class="text-center text-text-secondary py-12">No expenses found.</td></tr>';
            return;
        }
        tbody.innerHTML = expenses.map(ex => {
            const date = ex.date ? new Date(ex.date).toLocaleDateString('en-GB', { day: '2-digit', month: 'short', year: 'numeric' }) : '—';
            return `<tr>
                <td class="font-medium">${date}</td>
                <td><span class="text-xs bg-slate-100 text-slate-600 px-2 py-1 rounded-md font-medium">${ex.category}</span></td>
                <td class="text-slate-500 text-sm max-w-xs truncate">${ex.description || '—'}</td>
                <td class="font-bold text-slate-800">${formatCurrency(ex.amount)}</td>
                <td>
                    <div class="flex items-center gap-2">
                        <button onclick='openEditExpense(${JSON.stringify(ex)})' class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-slate-50 hover:bg-primary/10 hover:text-primary text-slate-600 rounded-lg text-xs font-semibold border border-slate-100 transition-colors cursor-pointer">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>Edit
                        </button>
                        <button onclick="deleteExpense(${ex.id})" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-rose-50/50 hover:bg-rose-500/10 hover:text-rose-600 text-rose-600 rounded-lg text-xs font-semibold border border-rose-100/50 transition-colors cursor-pointer">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>Delete
                        </button>
                    </div>
                </td>
            </tr>`;
        }).join('');
    }

    function openAddExpense() {
        document.getElementById('expense_id').value = '';
        document.getElementById('expense_date').value = new Date().toISOString().split('T')[0];
        document.getElementById('expense_category').value = '';
        document.getElementById('expense_description').value = '';
        document.getElementById('expense_amount').value = '';
        document.getElementById('expense-modal-title').textContent = 'Add Expense';
        document.getElementById('expense-submit-btn').textContent = 'Save Expense';
        openModal('expenseModal');
    }

    function openEditExpense(ex) {
        document.getElementById('expense_id').value = ex.id;
        document.getElementById('expense_date').value = ex.date || '';
        document.getElementById('expense_category').value = ex.category || '';
        document.getElementById('expense_description').value = ex.description || '';
        document.getElementById('expense_amount').value = ex.amount || '';
        document.getElementById('expense-modal-title').textContent = 'Edit Expense';
        document.getElementById('expense-submit-btn').textContent = 'Update Expense';
        openModal('expenseModal');
    }

    async function submitExpense(e) {
        e.preventDefault();
        const id = document.getElementById('expense_id').value;
        const isEdit = !!id;
        const btn = e.target.querySelector('[type=submit]');
        btn.disabled = true; btn.textContent = 'Saving...';
        const payload = {
            date: document.getElementById('expense_date').value,
            category: document.getElementById('expense_category').value,
            description: document.getElementById('expense_description').value,
            amount: document.getElementById('expense_amount').value,
        };
        const data = isEdit
            ? await apiRequest('PUT', '/expenses/' + id, payload)
            : await apiRequest('POST', '/expenses', payload);
        btn.disabled = false; btn.textContent = isEdit ? 'Update Expense' : 'Save Expense';
        if (data && data.success) {
            closeModal('expenseModal');
            showToast(isEdit ? 'Expense updated.' : 'Expense added.');
            loadExpenses();
        }
    }

    async function deleteExpense(id) {
        if (!confirm('Delete this expense?')) return;
        const data = await apiRequest('DELETE', '/expenses/' + id);
        if (data && data.success) {
            showToast('Expense deleted.');
            loadExpenses();
        }
    }

    let searchTimer;
    document.addEventListener('DOMContentLoaded', () => {
        document.getElementById('expense-search').addEventListener('input', function() {
            clearTimeout(searchTimer);
            searchTimer = setTimeout(() => loadExpenses(this.value), 350);
        });

        loadExpenses();
    });
</script>
@endpush
