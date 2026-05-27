<div class="card">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-4 pb-2 border-b border-slate-100">
        <div>
            <h3 class="text-sm font-bold text-slate-800 uppercase tracking-wider">Stock Level Alert Monitor</h3>
            <p class="text-[11px] text-slate-400">Products that are at or below safety reorder thresholds</p>
        </div>
        <div>
            <input wire:model.live.debounce.250ms="search" type="text" placeholder="Filter alerts..." class="form-input text-xs py-1.5 px-3 w-48" />
        </div>
    </div>

    <div class="space-y-4">
        @forelse($lowStockProducts as $p)
            @php
                // Calculate percentage relative to reorder level (up to 100%)
                $reorder = (float)$p->reorder_level ?: 1;
                $pct = min(100, max(0, ((float)$p->current_stock / $reorder) * 100));
                
                // Color status bar depending on critical levels
                $barColor = $pct <= 25 ? 'bg-rose-500 shadow-rose-500/20' : ($pct <= 75 ? 'bg-amber-500 shadow-amber-500/20' : 'bg-indigo-500 shadow-indigo-500/20');
            @endphp
            <div class="p-3 bg-slate-50 border border-slate-100 rounded-2xl">
                <div class="flex items-center justify-between mb-1.5">
                    <div>
                        <span class="inline-flex font-mono text-[9px] font-bold text-primary bg-primary/5 px-2 py-0.5 rounded-md mb-1">{{ $p->product_code }}</span>
                        <h4 class="text-xs font-bold text-slate-800">{{ $p->name }}</h4>
                        <p class="text-[9px] text-slate-400 font-semibold">{{ data_get($p, 'categoryRelation.category_name', 'General') }}</p>
                    </div>
                    <div class="text-right">
                        <span class="text-xs font-extrabold text-rose-500">{{ (int)$p->current_stock }} / {{ (int)$p->reorder_level }}</span>
                        <p class="text-[9px] text-slate-400 font-medium">Stock / Reorder</p>
                    </div>
                </div>
                
                {{-- Progress Bar --}}
                <div class="w-full bg-slate-200/80 rounded-full h-1.5 overflow-hidden mt-2 relative">
                    <div class="{{ $barColor }} h-full rounded-full transition-all duration-500 shadow-sm" style="width: {{ $pct }}%"></div>
                </div>
            </div>
        @empty
            <div class="text-center text-text-secondary py-8 text-xs font-semibold">
                🎉 Excellent! All products are currently above their reorder levels.
            </div>
        @endforelse
    </div>
</div>
