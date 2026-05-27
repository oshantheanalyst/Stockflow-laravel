@extends('layouts.app')
@section('title', 'Business Reports')
@section('content')

<div class="card mb-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-2xl font-extrabold tracking-tight text-slate-800">Business Reports</h2>
            <p class="text-sm text-slate-400 mt-1">Financial summary and performance analysis</p>
        </div>
        <div class="flex items-center gap-3 flex-wrap">
            <input type="date" id="report-start" class="form-input" />
            <span class="text-slate-400 text-sm">to</span>
            <input type="date" id="report-end" class="form-input" />
            <button onclick="loadReport()" class="btn-primary flex items-center gap-1.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                Generate Report
            </button>
        </div>
    </div>
</div>

<div id="report-content">
    {{-- Populated by JS from GET /api/reports --}}
    <div id="reports-loading" class="card flex items-center justify-center py-16" style="display:none !important;">
        <div class="flex items-center gap-2">
            <svg class="animate-spin h-5 w-5 text-primary" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
            <span class="text-primary font-bold text-sm">Generating report...</span>
        </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 mb-6">
        <div class="stat-card">
            <span class="text-[10px] font-extrabold text-slate-400 uppercase tracking-wider">Total Sales</span>
            <div class="mt-2"><span id="report-total-sales" class="text-2xl font-extrabold text-slate-800">—</span></div>
            <p class="text-[10px] text-slate-400 mt-0.5"><span id="report-invoice-count">—</span> invoices</p>
        </div>
        <div class="stat-card">
            <span class="text-[10px] font-extrabold text-slate-400 uppercase tracking-wider">Total Expenses</span>
            <div class="mt-2"><span id="report-total-expenses" class="text-2xl font-extrabold text-rose-600">—</span></div>
        </div>
        <div class="stat-card">
            <span class="text-[10px] font-extrabold text-slate-400 uppercase tracking-wider">Total Discount</span>
            <div class="mt-2"><span id="report-total-discount" class="text-2xl font-extrabold text-amber-500">—</span></div>
        </div>
        <div class="stat-card">
            <span class="text-[10px] font-extrabold text-slate-400 uppercase tracking-wider">Net Profit</span>
            <div class="mt-2"><span id="report-net-profit" class="text-2xl font-extrabold text-emerald-600">—</span></div>
        </div>
    </div>

    {{-- ── Charts Row ─────────────────────────────────────── --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        {{-- Sales Overview Bar Chart --}}
        <div class="card">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-extrabold text-slate-800">Sales Overview</h3>
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider bg-slate-100 px-2 py-1 rounded-lg">Bar Chart</span>
            </div>
            <div class="relative" style="height:220px">
                <canvas id="chart-sales-overview"></canvas>
            </div>
        </div>

        {{-- Top Products Doughnut --}}
        <div class="card">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-extrabold text-slate-800">Revenue by Product</h3>
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider bg-slate-100 px-2 py-1 rounded-lg">Doughnut</span>
            </div>
            <div class="relative" style="height:220px">
                <canvas id="chart-top-products"></canvas>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- Top Products --}}
        <div class="card">
            <h3 class="text-lg font-extrabold text-slate-800 mb-4">Top Products by Revenue</h3>
            <table class="data-table">
                <thead><tr><th>PRODUCT</th><th>QTY SOLD</th><th>REVENUE</th><th>PROFIT</th></tr></thead>
                <tbody id="report-top-products">
                    <tr><td colspan="4" class="text-center text-slate-400 py-8">Generate report to view data.</td></tr>
                </tbody>
            </table>
        </div>

        {{-- Right Column --}}
        <div class="flex flex-col gap-6">
            {{-- Expenses by Category --}}
            <div class="card flex-1">
                <h3 class="text-lg font-extrabold text-slate-800 mb-4">Expenses by Category</h3>
                <table class="data-table">
                    <thead><tr><th>CATEGORY</th><th>TOTAL</th></tr></thead>
                    <tbody id="report-expenses-by-cat">
                        <tr><td colspan="2" class="text-center text-slate-400 py-8">Generate report to view data.</td></tr>
                    </tbody>
                </table>
            </div>

            {{-- External API Integration Rate Card --}}
            <div class="card shadow-sm p-6 border-l-4 border-indigo-500 bg-gradient-to-br from-white to-indigo-50/10">
                <div class="flex items-center justify-between pb-3 mb-3 border-b border-slate-200">
                    <div>
                        <h3 class="text-sm font-bold text-slate-800 flex items-center gap-1.5">
                            🌍 Global Exchange Rates
                        </h3>
                        <p class="text-[11px] text-slate-500">Base Currency: LKR. Updated Live.</p>
                    </div>
                    <span class="inline-flex h-2.5 w-2.5 rounded-full bg-indigo-500 animate-ping"></span>
                </div>
                <div class="grid grid-cols-3 gap-3" id="report-exchange-rates">
                    <div class="col-span-3 text-center text-xs text-slate-400 py-4 font-semibold">Loading exchange rates...</div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
<script>
    /**
     * REPORTS PAGE — Data from GET /api/reports?start_date=&end_date=
     * Admin only — enforced by api.php CheckAdmin middleware
     */

    let _chartOverview = null;
    let _chartProducts = null;

    const CHART_COLORS = [
        '#6366f1', '#10b981', '#f59e0b', '#ef4444',
        '#3b82f6', '#8b5cf6', '#ec4899', '#14b8a6'
    ];

    function drawOverviewChart(sales, expenses, profit) {
        const ctx = document.getElementById('chart-sales-overview').getContext('2d');
        if (_chartOverview) _chartOverview.destroy();
        _chartOverview = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ['Total Sales', 'Total Expenses', 'Net Profit'],
                datasets: [{
                    label: 'Amount (LKR)',
                    data: [sales, expenses, profit],
                    backgroundColor: [
                        'rgba(99,102,241,0.85)',
                        'rgba(239,68,68,0.85)',
                        'rgba(16,185,129,0.85)'
                    ],
                    borderColor: [
                        '#6366f1', '#ef4444', '#10b981'
                    ],
                    borderWidth: 2,
                    borderRadius: 8,
                    borderSkipped: false
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: ctx => ' LKR ' + parseFloat(ctx.raw || 0).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })
                        }
                    }
                },
                scales: {
                    x: { grid: { display: false }, ticks: { font: { weight: '700', size: 11 } } },
                    y: {
                        grid: { color: 'rgba(148,163,184,0.12)' },
                        ticks: {
                            font: { size: 10 },
                            callback: v => 'LKR ' + (v >= 1000 ? (v/1000).toFixed(0)+'k' : v)
                        }
                    }
                }
            }
        });
    }

    function drawProductsChart(products) {
        const ctx = document.getElementById('chart-top-products').getContext('2d');
        if (_chartProducts) _chartProducts.destroy();
        if (!products || !products.length) {
            _chartProducts = null;
            return;
        }
        const top = products.slice(0, 7);
        _chartProducts = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: top.map(p => p.name),
                datasets: [{
                    data: top.map(p => parseFloat(p.revenue) || 0),
                    backgroundColor: CHART_COLORS.slice(0, top.length),
                    borderColor: '#fff',
                    borderWidth: 3,
                    hoverOffset: 8
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '62%',
                plugins: {
                    legend: {
                        position: 'right',
                        labels: { font: { size: 11, weight: '600' }, boxWidth: 12, padding: 14, usePointStyle: true }
                    },
                    tooltip: {
                        callbacks: {
                            label: ctx => ' LKR ' + parseFloat(ctx.raw || 0).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })
                        }
                    }
                }
            }
        });
    }

    document.addEventListener('DOMContentLoaded', () => {
        // Set default date range: this month
        const today = new Date();
        const firstDay = new Date(today.getFullYear(), today.getMonth(), 1).toISOString().split('T')[0];
        const todayStr = today.toISOString().split('T')[0];
        document.getElementById('report-start').value = firstDay;
        document.getElementById('report-end').value = todayStr;
        
        loadReport();
    });

    async function loadReport() {
        const start = document.getElementById('report-start').value;
        const end = document.getElementById('report-end').value;

        document.getElementById('report-total-sales').textContent = '...';
        document.getElementById('report-total-expenses').textContent = '...';
        document.getElementById('report-total-discount').textContent = '...';
        document.getElementById('report-net-profit').textContent = '...';
        document.getElementById('report-top-products').innerHTML = '<tr><td colspan="4" class="text-center py-6"><svg class="animate-spin h-5 w-5 text-primary mx-auto" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg></td></tr>';
        document.getElementById('report-expenses-by-cat').innerHTML = '<tr><td colspan="2" class="text-center py-6">...</td></tr>';

        const data = await apiRequest('GET', '/reports?start_date=' + start + '&end_date=' + end);
        if (!data || !data.data) return;

        const d = data.data;
        document.getElementById('report-total-sales').textContent = formatCurrency(d.total_sales);
        document.getElementById('report-total-expenses').textContent = formatCurrency(d.total_expenses);
        document.getElementById('report-total-discount').textContent = formatCurrency(d.total_discount);
        document.getElementById('report-net-profit').textContent = formatCurrency(d.net_profit);

        drawOverviewChart(
            parseFloat(d.total_sales)    || 0,
            parseFloat(d.total_expenses) || 0,
            parseFloat(d.net_profit)     || 0
        );
        drawProductsChart(d.top_products || []);
        document.getElementById('report-invoice-count').textContent = d.invoice_count || 0;

        // Top products
        const topProducts = d.top_products || [];
        document.getElementById('report-top-products').innerHTML = topProducts.length
            ? topProducts.map(p => `<tr>
                <td class="font-semibold text-slate-700">${p.name}</td>
                <td>${(p.qty_sold || 0).toLocaleString()}</td>
                <td class="font-bold">${formatCurrency(p.revenue)}</td>
                <td class="${p.profit >= 0 ? 'text-emerald-600' : 'text-rose-600'} font-bold">${formatCurrency(p.profit)}</td>
            </tr>`).join('')
            : '<tr><td colspan="4" class="text-center text-slate-400 py-8">No sales data for this period.</td></tr>';

        // Expenses by category
        const expBycat = d.expenses_by_category || [];
        document.getElementById('report-expenses-by-cat').innerHTML = expBycat.length
            ? expBycat.map(e => `<tr>
                <td><span class="text-xs bg-slate-100 text-slate-600 px-2 py-1 rounded font-medium">${e.category}</span></td>
                <td class="font-bold text-slate-800">${formatCurrency(e.total)}</td>
            </tr>`).join('')
            : '<tr><td colspan="2" class="text-center text-slate-400 py-8">No expenses for this period.</td></tr>';

        // Exchange rates
        const rates = d.exchange_rates || null;
        if (rates) {
            document.getElementById('report-exchange-rates').innerHTML = `
                <div class="bg-slate-50 hover:bg-indigo-50/30 transition-colors rounded-xl p-3 border border-slate-200 flex flex-col items-center justify-center text-center">
                    <span class="text-[10px] font-bold text-slate-500 uppercase mb-1">United States</span>
                    <span class="text-sm font-extrabold text-indigo-600 font-mono">${parseFloat(rates.USD || 0).toFixed(2)}</span>
                    <span class="text-[9px] font-semibold text-slate-400 uppercase mt-0.5">USD</span>
                </div>
                <div class="bg-slate-50 hover:bg-indigo-50/30 transition-colors rounded-xl p-3 border border-slate-200 flex flex-col items-center justify-center text-center">
                    <span class="text-[10px] font-bold text-slate-500 uppercase mb-1">Australia</span>
                    <span class="text-sm font-extrabold text-indigo-600 font-mono">${parseFloat(rates.AUD || 0).toFixed(2)}</span>
                    <span class="text-[9px] font-semibold text-slate-400 uppercase mt-0.5">AUD</span>
                </div>
                <div class="bg-slate-50 hover:bg-indigo-50/30 transition-colors rounded-xl p-3 border border-slate-200 flex flex-col items-center justify-center text-center">
                    <span class="text-[10px] font-bold text-slate-500 uppercase mb-1">Eurozone</span>
                    <span class="text-sm font-extrabold text-indigo-600 font-mono">${parseFloat(rates.EUR || 0).toFixed(2)}</span>
                    <span class="text-[9px] font-semibold text-slate-400 uppercase mt-0.5">EUR</span>
                </div>
            `;
        } else {
            document.getElementById('report-exchange-rates').innerHTML = '<div class="col-span-3 text-center text-xs text-rose-500 py-4 font-semibold">Failed to fetch exchange rates.</div>';
        }
    }

</script>
@endpush
