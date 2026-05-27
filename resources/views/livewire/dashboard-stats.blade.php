<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 mb-6">
    {{-- Sales valuation card --}}
    <div class="stat-card">
        <div class="flex items-center justify-between">
            <span class="text-[10px] font-extrabold text-slate-400 uppercase tracking-wider">Total Sales</span>
            <span class="p-2 rounded-xl bg-indigo-500/10 text-indigo-500">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </span>
        </div>
        <div class="mt-2">
            <span class="text-2xl font-extrabold text-slate-800">Rs {{ number_format($totalSales, 2) }}</span>
            <p class="text-[10px] text-slate-400 mt-0.5">Accumulated sales volume</p>
        </div>
    </div>

    {{-- Orders Count Card --}}
    <div class="stat-card">
        <div class="flex items-center justify-between">
            <span class="text-[10px] font-extrabold text-slate-400 uppercase tracking-wider">Invoices Raised</span>
            <span class="p-2 rounded-xl bg-emerald-500/10 text-emerald-500">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            </span>
        </div>
        <div class="mt-2">
            <span class="text-2xl font-extrabold text-slate-800">{{ number_format($ordersCount) }}</span>
            <p class="text-[10px] text-slate-400 mt-0.5">Total processed transactions</p>
        </div>
    </div>

    {{-- Product count card --}}
    <div class="stat-card">
        <div class="flex items-center justify-between">
            <span class="text-[10px] font-extrabold text-slate-400 uppercase tracking-wider">Product Inventory</span>
            <span class="p-2 rounded-xl bg-amber-500/10 text-amber-500">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
            </span>
        </div>
        <div class="mt-2">
            <span class="text-2xl font-extrabold text-slate-800">{{ number_format($productsCount) }}</span>
            <p class="text-[10px] text-slate-400 mt-0.5">Unique inventory units registered</p>
        </div>
    </div>

    {{-- Average order value card --}}
    <div class="stat-card">
        <div class="flex items-center justify-between">
            <span class="text-[10px] font-extrabold text-slate-400 uppercase tracking-wider">Avg Order Value</span>
            <span class="p-2 rounded-xl bg-sky-500/10 text-sky-500">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
            </span>
        </div>
        <div class="mt-2">
            <span class="text-2xl font-extrabold text-slate-800">Rs {{ number_format($avgOrderValue, 2) }}</span>
            <p class="text-[10px] text-slate-400 mt-0.5">Mean checkout total value</p>
        </div>
    </div>
</div>
