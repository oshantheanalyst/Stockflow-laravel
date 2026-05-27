@extends('layouts.app')
@section('title', 'Reports')
@section('content')

{{-- Header with date filters --}}
<div class="card mb-6 border-l-4 border-primary p-6 bg-gradient-to-r from-white to-gray-50/50 shadow-md transition-all duration-300 hover:shadow-lg">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-6">
        <div>
            <h2 class="text-2xl font-bold text-text-primary flex items-center gap-2">
                <span class="p-2 bg-primary/10 rounded-lg text-primary text-lg">📊</span>
                Reports & Analytics
            </h2>
            <p class="text-sm text-text-secondary mt-1 font-medium flex items-center gap-1.5">
                <span class="inline-block w-2.5 h-2.5 rounded-full bg-success animate-pulse"></span>
                Active Period: <span class="text-text-primary font-semibold">{{ $dateRangeLabel }}</span>
            </p>
        </div>
        <form method="GET" class="flex flex-wrap items-center gap-4 bg-white p-3 rounded-xl border border-border shadow-sm">
            <div class="flex items-center gap-2">
                <span class="text-xs font-bold text-text-secondary uppercase tracking-wider">From:</span>
                <input type="date" name="start_date" value="{{ $startDate }}" class="form-input w-40 border-gray-200 focus:border-primary focus:ring-1 focus:ring-primary/20 rounded-lg" />
            </div>
            <div class="flex items-center gap-2">
                <span class="text-xs font-bold text-text-secondary uppercase tracking-wider">To:</span>
                <input type="date" name="end_date" value="{{ $endDate }}" class="form-input w-40 border-gray-200 focus:border-primary focus:ring-1 focus:ring-primary/20 rounded-lg" />
            </div>
            <button type="submit" class="btn-primary flex items-center gap-2 py-2 px-5 shadow-sm hover:shadow-primary/30 transition-all duration-200">
                <span>🔄</span> Generate Report
            </button>
        </form>
    </div>
</div>

{{-- Summary Cards with rich styling and micro-animations --}}
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
    {{-- Card 1: Total Sales --}}
    <div class="group bg-gradient-to-br from-white to-blue-50/10 rounded-2xl border border-border p-6 shadow-sm hover:shadow-md transition-all duration-300 hover:-translate-y-1 relative overflow-hidden">
        <div class="absolute top-0 right-0 w-24 h-24 bg-blue/5 rounded-full -mr-8 -mt-8 transition-transform group-hover:scale-125 duration-500"></div>
        <div class="flex justify-between items-start mb-4">
            <span class="text-xs font-extrabold text-text-secondary tracking-wider uppercase">Total Revenue</span>
            <span class="p-2 bg-blue-500/10 text-info rounded-xl text-lg group-hover:bg-blue-500/20 transition-colors">💰</span>
        </div>
        <h3 class="text-2xl font-extrabold text-text-primary group-hover:text-blue transition-colors">Rs {{ number_format($totalSales, 2) }}</h3>
        <p class="text-xs text-text-secondary mt-2 flex items-center gap-1.5 font-medium">
            <span class="px-1.5 py-0.5 bg-blue-100 text-blue font-semibold rounded text-[10px]">{{ $invoiceCount }}</span> 
            Completed Sales Invoices
        </p>
    </div>

    {{-- Card 2: Total Discount --}}
    <div class="group bg-gradient-to-br from-white to-amber-50/10 rounded-2xl border border-border p-6 shadow-sm hover:shadow-md transition-all duration-300 hover:-translate-y-1 relative overflow-hidden">
        <div class="absolute top-0 right-0 w-24 h-24 bg-warning/5 rounded-full -mr-8 -mt-8 transition-transform group-hover:scale-125 duration-500"></div>
        <div class="flex justify-between items-start mb-4">
            <span class="text-xs font-extrabold text-text-secondary tracking-wider uppercase">Discounts Issued</span>
            <span class="p-2 bg-warning/10 text-warning rounded-xl text-lg group-hover:bg-warning/20 transition-colors">🏷️</span>
        </div>
        <h3 class="text-2xl font-extrabold text-text-primary group-hover:text-warning transition-colors">Rs {{ number_format($totalDiscount, 2) }}</h3>
        <p class="text-xs text-text-secondary mt-2 flex items-center gap-1">
            <span class="inline-block w-1.5 h-1.5 rounded-full bg-warning"></span>
            Promotional price reductions
        </p>
    </div>

    {{-- Card 3: Total Expenses --}}
    <div class="group bg-gradient-to-br from-white to-red-50/10 rounded-2xl border border-border p-6 shadow-sm hover:shadow-md transition-all duration-300 hover:-translate-y-1 relative overflow-hidden">
        <div class="absolute top-0 right-0 w-24 h-24 bg-danger/5 rounded-full -mr-8 -mt-8 transition-transform group-hover:scale-125 duration-500"></div>
        <div class="flex justify-between items-start mb-4">
            <span class="text-xs font-extrabold text-text-secondary tracking-wider uppercase">Operating Costs</span>
            <span class="p-2 bg-danger/10 text-danger rounded-xl text-lg group-hover:bg-danger/20 transition-colors">💸</span>
        </div>
        <h3 class="text-2xl font-extrabold text-text-primary group-hover:text-danger transition-colors">Rs {{ number_format($totalExpenses, 2) }}</h3>
        <p class="text-xs text-text-secondary mt-2 flex items-center gap-1">
            <span class="inline-block w-1.5 h-1.5 rounded-full bg-danger animate-pulse"></span>
            Expenses recorded in period
        </p>
    </div>

    {{-- Card 4: Net Profit --}}
    <div class="group bg-gradient-to-br from-white to-emerald-50/10 rounded-2xl border border-border p-6 shadow-sm hover:shadow-md transition-all duration-300 hover:-translate-y-1 relative overflow-hidden">
        <div class="absolute top-0 right-0 w-24 h-24 bg-success/5 rounded-full -mr-8 -mt-8 transition-transform group-hover:scale-125 duration-500"></div>
        <div class="flex justify-between items-start mb-4">
            <span class="text-xs font-extrabold text-text-secondary tracking-wider uppercase">Net Profit Margin</span>
            <span class="p-2 bg-success/10 text-success rounded-xl text-lg group-hover:bg-success/20 transition-colors">📈</span>
        </div>
        <h3 class="text-2xl font-extrabold @if($netProfit >= 0) text-success @else text-danger @endif transition-colors">Rs {{ number_format($netProfit, 2) }}</h3>
        <p class="text-xs text-text-secondary mt-2 flex items-center gap-1">
            <span class="inline-block w-1.5 h-1.5 rounded-full @if($netProfit >= 0) bg-success @else bg-danger @endif"></span>
            Revenue minus COGS and expenses
        </p>
    </div>
</div>

{{-- Interactive Charts Section --}}
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
    {{-- Sales Performance Chart --}}
    <div class="card lg:col-span-2 shadow-sm flex flex-col p-6">
        <div class="flex items-center justify-between border-b border-border pb-4 mb-4">
            <div>
                <h3 class="text-lg font-bold text-text-primary">Sales & profitability Performance</h3>
                <p class="text-xs text-text-secondary mt-0.5">Top products ranked by sales revenue and net profits</p>
            </div>
            <span class="text-xs font-bold bg-primary/10 text-primary px-2.5 py-1 rounded-full uppercase">Top 10 Products</span>
        </div>
        <div class="relative flex-1 min-h-[300px] flex items-center justify-center">
            @if(count($topProducts) > 0)
                <canvas id="salesPerformanceChart"></canvas>
            @else
                <p class="text-sm text-text-secondary py-12">No sales data available for chart visualization.</p>
            @endif
        </div>
    </div>

    {{-- Expense Distribution Chart --}}
    <div class="card shadow-sm flex flex-col p-6">
        <div class="flex items-center justify-between border-b border-border pb-4 mb-4">
            <div>
                <h3 class="text-lg font-bold text-text-primary">Cost Categories</h3>
                <p class="text-xs text-text-secondary mt-0.5">Categorized breakdown of expenses</p>
            </div>
            <span class="text-xs font-bold bg-danger/10 text-danger px-2.5 py-1 rounded-full uppercase">Expenses breakdown</span>
        </div>
        <div class="relative flex-1 min-h-[250px] flex items-center justify-center">
            @if(count($expensesByCategory) > 0)
                <canvas id="expenseCategoryChart"></canvas>
            @else
                <p class="text-sm text-text-secondary py-12">No expense data available for visualization.</p>
            @endif
        </div>
    </div>
</div>

{{-- Tables Layout --}}
<div class="grid grid-cols-1 lg:grid-cols-12 gap-6 mb-6">
    {{-- Top Products Details Table --}}
    <div class="card lg:col-span-7 shadow-sm p-6">
        <div class="flex items-center justify-between border-b border-border pb-4 mb-4">
            <h3 class="text-base font-bold text-text-primary flex items-center gap-2">
                <span>📦</span> Top Selling Products
            </h3>
        </div>
        <div class="overflow-x-auto">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>PRODUCT NAME</th>
                        <th class="text-center">QTY SOLD</th>
                        <th>REVENUE</th>
                        <th>PROFIT</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($topProducts as $tp)
                    <tr class="hover:bg-gray-50/50 transition-colors">
                        <td class="font-semibold text-text-primary py-3.5">{{ $tp['name'] }}</td>
                        <td class="text-center font-mono py-3.5"><span class="px-2.5 py-0.5 bg-gray-100 text-text-primary rounded-full text-xs font-medium">{{ $tp['qty_sold'] }}</span></td>
                        <td class="font-mono text-text-secondary py-3.5 font-medium">Rs {{ number_format($tp['revenue'], 2) }}</td>
                        <td class="py-3.5"><span class="font-bold text-success font-mono">Rs {{ number_format($tp['profit'], 2) }}</span></td>
                    </tr>
                    @empty
                    <tr><td colspan="4" class="text-center text-text-secondary py-12">No product sales recorded in this period.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Expenses by Category details and Exchange Rates --}}
    <div class="lg:col-span-5 flex flex-col gap-6">
        <div class="card shadow-sm p-6 flex-1">
            <div class="flex items-center justify-between border-b border-border pb-4 mb-4">
                <h3 class="text-base font-bold text-text-primary flex items-center gap-2">
                    <span>💸</span> Expenses List
                </h3>
            </div>
            <div class="overflow-x-auto">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>CATEGORY</th>
                            <th class="text-right">TOTAL AMOUNT</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($expensesByCategory as $exp)
                        <tr class="hover:bg-gray-50/50 transition-colors">
                            <td class="font-semibold text-text-primary py-3.5">{{ $exp['category'] }}</td>
                            <td class="text-right py-3.5"><span class="font-bold text-danger font-mono">Rs {{ number_format($exp['total'], 2) }}</span></td>
                        </tr>
                        @empty
                        <tr><td colspan="2" class="text-center text-text-secondary py-12">No expenses found for this period.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- External API Integration Rate Card --}}
        <div class="card shadow-sm p-6 border-l-4 border-indigo-500 bg-gradient-to-br from-white to-indigo-50/10">
            <div class="flex items-center justify-between pb-3 mb-3 border-b border-border/60">
                <div>
                    <h3 class="text-sm font-bold text-text-primary flex items-center gap-1.5">
                        🌍 Global Exchange Rates
                    </h3>
                    <p class="text-[11px] text-text-secondary">Base Currency: LKR. Updated Live.</p>
                </div>
                <span class="inline-flex h-2.5 w-2.5 rounded-full bg-indigo-500 animate-ping"></span>
            </div>
            <div class="grid grid-cols-3 gap-3">
                @if(isset($exchangeRates))
                    <div class="bg-gray-50 hover:bg-indigo-50/30 transition-colors rounded-xl p-3 border border-border flex flex-col items-center justify-center text-center">
                        <span class="text-[10px] font-bold text-text-secondary uppercase mb-1">United States</span>
                        <span class="text-sm font-extrabold text-indigo-600 font-mono">{{ number_format($exchangeRates['USD'], 2) }}</span>
                        <span class="text-[9px] font-semibold text-text-secondary uppercase mt-0.5">USD</span>
                    </div>
                    <div class="bg-gray-50 hover:bg-indigo-50/30 transition-colors rounded-xl p-3 border border-border flex flex-col items-center justify-center text-center">
                        <span class="text-[10px] font-bold text-text-secondary uppercase mb-1">Australia</span>
                        <span class="text-sm font-extrabold text-indigo-600 font-mono">{{ number_format($exchangeRates['AUD'], 2) }}</span>
                        <span class="text-[9px] font-semibold text-text-secondary uppercase mt-0.5">AUD</span>
                    </div>
                    <div class="bg-gray-50 hover:bg-indigo-50/30 transition-colors rounded-xl p-3 border border-border flex flex-col items-center justify-center text-center">
                        <span class="text-[10px] font-bold text-text-secondary uppercase mb-1">Eurozone</span>
                        <span class="text-sm font-extrabold text-indigo-600 font-mono">{{ number_format($exchangeRates['EUR'], 2) }}</span>
                        <span class="text-[9px] font-semibold text-text-secondary uppercase mt-0.5">EUR</span>
                    </div>
                @else
                    <div class="col-span-3 text-center text-xs text-danger py-4 font-semibold">Failed to fetch exchange rates.</div>
                @endif
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
{{-- Load ChartJS --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    // 1. Sales Performance Chart: Double Bar Chart for Revenue vs Profit
    const topProducts = @json($topProducts);
    if (topProducts && topProducts.length > 0) {
        const labels = topProducts.map(p => p.name);
        const revenues = topProducts.map(p => p.revenue);
        const profits = topProducts.map(p => p.profit);

        const salesCtx = document.getElementById('salesPerformanceChart').getContext('2d');
        new Chart(salesCtx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [
                    {
                        label: 'Revenue (Rs)',
                        data: revenues,
                        backgroundColor: 'rgba(90, 88, 245, 0.75)',
                        borderColor: '#5A58F5',
                        borderWidth: 1.5,
                        borderRadius: 6,
                        hoverBackgroundColor: 'rgba(90, 88, 245, 0.9)',
                    },
                    {
                        label: 'Profit (Rs)',
                        data: profits,
                        backgroundColor: 'rgba(16, 185, 129, 0.75)',
                        borderColor: '#10B981',
                        borderWidth: 1.5,
                        borderRadius: 6,
                        hoverBackgroundColor: 'rgba(16, 185, 129, 0.9)',
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                        labels: {
                            font: { family: 'Figtree', weight: 'bold', size: 11 },
                            color: '#4B5563',
                            usePointStyle: true,
                            padding: 15
                        }
                    },
                    tooltip: {
                        padding: 12,
                        backgroundColor: '#1E2336',
                        titleFont: { family: 'Figtree', weight: 'bold', size: 12 },
                        bodyFont: { family: 'Figtree', size: 12 },
                        cornerRadius: 8,
                        boxPadding: 4
                    }
                },
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: {
                            color: '#6B7280',
                            font: { family: 'Figtree', size: 10, weight: 'medium' }
                        }
                    },
                    y: {
                        grid: { color: '#E5E7EB', drawTicks: false },
                        border: { dash: [4, 4] },
                        ticks: {
                            color: '#6B7280',
                            font: { family: 'Figtree', size: 10, weight: 'medium' },
                            callback: function(value) {
                                return value >= 1000 ? 'Rs ' + (value / 1000) + 'k' : 'Rs ' + value;
                            }
                        }
                    }
                }
            }
        });
    }

    // 2. Expense Category Breakdown Chart: Doughnut Chart
    const expensesByCategory = @json($expensesByCategory);
    if (expensesByCategory && expensesByCategory.length > 0) {
        const expLabels = expensesByCategory.map(e => e.category);
        const expTotals = expensesByCategory.map(e => e.total);

        const expColors = [
            'rgba(239, 68, 68, 0.75)', // Red
            'rgba(245, 158, 11, 0.75)', // Amber
            'rgba(59, 130, 246, 0.75)', // Blue
            'rgba(139, 92, 246, 0.75)', // Purple
            'rgba(236, 72, 153, 0.75)', // Pink
            'rgba(16, 185, 129, 0.75)', // Emerald
        ];
        const expBorderColors = [
            '#EF4444',
            '#F59E0B',
            '#3B82F6',
            '#8B5CF6',
            '#EC4899',
            '#10B981',
        ];

        const expenseCtx = document.getElementById('expenseCategoryChart').getContext('2d');
        new Chart(expenseCtx, {
            type: 'doughnut',
            data: {
                labels: expLabels,
                datasets: [{
                    data: expTotals,
                    backgroundColor: expColors.slice(0, expLabels.length),
                    borderColor: expBorderColors.slice(0, expLabels.length),
                    borderWidth: 1.5,
                    hoverOffset: 10
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '65%',
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            font: { family: 'Figtree', weight: 'semibold', size: 10 },
                            color: '#4B5563',
                            usePointStyle: true,
                            padding: 12
                        }
                    },
                    tooltip: {
                        padding: 12,
                        backgroundColor: '#1E2336',
                        titleFont: { family: 'Figtree', weight: 'bold', size: 12 },
                        bodyFont: { family: 'Figtree', size: 12 },
                        cornerRadius: 8,
                        boxPadding: 4,
                        callbacks: {
                            label: function(context) {
                                let label = context.label || '';
                                if (label) { label += ': '; }
                                label += 'Rs ' + parseFloat(context.parsed).toLocaleString('en-US', { minimumFractionDigits: 2 });
                                return label;
                            }
                        }
                    }
                }
            }
        });
    }
});
</script>
@endpush
