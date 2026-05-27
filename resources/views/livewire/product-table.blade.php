<div>
    {{-- Stats Grid --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 mb-6">
        <div class="stat-card">
            <div class="flex items-center justify-between">
                <span class="text-[10px] font-extrabold text-slate-400 uppercase tracking-wider">Total Products</span>
                <span class="p-2 rounded-xl bg-indigo-500/10 text-indigo-500">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                </span>
            </div>
            <div class="mt-2">
                <span class="text-2xl font-extrabold text-slate-800">{{ $totalProducts }}</span>
                <p class="text-[10px] text-slate-400 mt-0.5">Unique items registered</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="flex items-center justify-between">
                <span class="text-[10px] font-extrabold text-slate-400 uppercase tracking-wider">Low Stock Alerts</span>
                <span class="p-2 rounded-xl {{ $lowStockCount > 0 ? 'bg-rose-500/10 text-rose-500 animate-pulse' : 'bg-slate-100 text-slate-400' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                </span>
            </div>
            <div class="mt-2">
                <span class="text-2xl font-extrabold {{ $lowStockCount > 0 ? 'text-rose-500' : 'text-slate-800' }}">{{ $lowStockCount }}</span>
                <p class="text-[10px] text-slate-400 mt-0.5">Below reorder levels</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="flex items-center justify-between">
                <span class="text-[10px] font-extrabold text-slate-400 uppercase tracking-wider">Total Stock Qty</span>
                <span class="p-2 rounded-xl bg-emerald-500/10 text-emerald-500">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                </span>
            </div>
            <div class="mt-2">
                <span class="text-2xl font-extrabold text-slate-800">{{ number_format($totalStockQty) }}</span>
                <p class="text-[10px] text-slate-400 mt-0.5">Physical items in inventory</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="flex items-center justify-between">
                <span class="text-[10px] font-extrabold text-slate-400 uppercase tracking-wider">Stock Valuation</span>
                <span class="p-2 rounded-xl bg-amber-500/10 text-amber-500">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </span>
            </div>
            <div class="mt-2">
                <span class="text-2xl font-extrabold text-slate-800">Rs {{ number_format($stockValuation, 2) }}</span>
                <p class="text-[10px] text-slate-400 mt-0.5">Estimated wholesale worth</p>
            </div>
        </div>
    </div>

    {{-- Filter Header --}}
    <div class="card mb-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="text-2xl font-extrabold tracking-tight text-slate-800">Products & Stock</h2>
                <p class="text-sm text-slate-400 mt-1">Monitor and manage your inventory in real-time</p>
            </div>
            <div class="flex items-center gap-3 flex-wrap">
                <div class="relative w-64">
                    <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </span>
                    <input wire:model.live.debounce.300ms="search" type="text" placeholder="Search code or name..." class="form-input pl-10 w-full" />
                </div>
                
                <select wire:model.live="categoryFilter" class="form-input w-48">
                    <option value="">All Categories</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}">{{ $cat->category_name }}</option>
                    @endforeach
                </select>

                <button wire:click="openAddModal" class="btn-primary flex items-center gap-1.5 cursor-pointer admin-only" style="display:none;">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                    <span>Add Item</span>
                </button>
            </div>
        </div>
    </div>

    {{-- Alert Messages --}}
    @if(session()->has('message'))
        <div class="mb-4 p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl text-xs font-semibold">
            {{ session('message') }}
        </div>
    @endif

    {{-- Data Table --}}
    <div class="card overflow-x-auto relative">
        <table class="data-table">
            <thead>
                <tr>
                    <th>PHOTO</th>
                    <th>CODE</th>
                    <th>NAME</th>
                    <th>CATEGORY</th>
                    <th>UNIT</th>
                    <th>BUY PRICE</th>
                    <th>SELL PRICE</th>
                    <th>STOCK STATUS</th>
                    <th>REORDER LVL</th>
                    <th class="admin-only" style="display:none;">ACTIONS</th>
                </tr>
            </thead>
            <tbody>
                @forelse($productResults as $p)
                    @php
                        $isLow = $p->current_stock <= $p->reorder_level;
                    @endphp
                    <tr>
                        <td>
                            @if(!empty($p->photo_url))
                                <img src="{{ $p->photo_url }}" alt="{{ $p->name }}" class="w-10 h-10 rounded-lg object-cover border border-slate-100 shadow-sm" />
                            @else
                                <div class="w-10 h-10 rounded-lg bg-slate-100 flex items-center justify-center">
                                    <svg class="w-5 h-5 text-slate-300" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909M13.5 12a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0z"/></svg>
                                </div>
                            @endif
                        </td>
                        <td>
                            <span class="inline-flex font-mono text-[11px] font-bold text-primary bg-primary/5 px-2.5 py-1 rounded-lg">
                                {{ $p->product_code }}
                            </span>
                        </td>
                        <td class="font-bold text-slate-800">{{ $p->name }}</td>
                        <td>
                            <span class="text-xs bg-slate-100 text-slate-600 px-2 py-1 rounded-md font-medium">
                                {{ data_get($p, 'categoryRelation.category_name', '—') }}
                            </span>
                        </td>
                        <td><span class="text-xs text-slate-500 font-medium">{{ $p->unit ?? 'Pcs' }}</span></td>
                        <td class="font-medium text-slate-700">Rs {{ number_format($p->buying_price, 2) }}</td>
                        <td class="font-bold text-slate-900">Rs {{ number_format($p->selling_price, 2) }}</td>
                        <td>
                            @if($isLow)
                                <span class="badge-danger">
                                    <span class="relative flex h-2 w-2">
                                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-rose-400 opacity-75"></span>
                                        <span class="relative inline-flex rounded-full h-2 w-2 bg-rose-500"></span>
                                    </span>
                                    <span>Low ({{ number_format($p->current_stock) }})</span>
                                </span>
                            @else
                                <span class="badge-success">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                    <span>In Stock ({{ number_format($p->current_stock) }})</span>
                                </span>
                            @endif
                        </td>
                            <td class="font-medium text-slate-500">{{ number_format($p->reorder_level) }}</td>
                            <td class="admin-only" style="display:none;">
                                <div class="flex items-center gap-2">
                                    <button type="button" wire:click="openEditModal({{ $p->id }})" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-indigo-50 hover:bg-indigo-100 hover:text-indigo-700 text-indigo-600 rounded-lg text-xs font-semibold border border-indigo-100 transition-colors cursor-pointer">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                                        Edit
                                    </button>
                                    <button wire:click="toggleActive({{ $p->id }})" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-slate-50 hover:bg-primary/10 hover:text-primary text-slate-600 rounded-lg text-xs font-semibold border border-slate-100 transition-colors cursor-pointer">
                                        {{ $p->is_active ? 'Deactivate' : 'Activate' }}
                                    </button>
                                </div>
                            </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="10" class="text-center text-text-secondary py-12">
                            No products found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        
        <div class="mt-4 px-4 py-2">
            {{ $productResults->links() }}
        </div>
    </div>

    {{-- Add Product Modal --}}
    <div id="addProductModal" wire:ignore.self class="modal-overlay" onclick="if(event.target===this)closeModal('addProductModal')">
        <div class="modal-content">
            <form wire:submit="saveProduct">
                <div class="px-6 py-4 border-b border-slate-100 flex justify-between items-center bg-white rounded-t-2xl">
                    <h3 class="text-lg font-bold text-slate-800 tracking-tight">Add New Item</h3>
                    <button type="button" onclick="closeModal('addProductModal')" class="text-slate-400 hover:text-slate-600 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
                
                <div class="px-6 py-5 space-y-4 max-h-[70vh] overflow-y-auto">
                    <!-- Error messages -->
                    @if(session()->has('error'))
                        <div class="p-3 bg-rose-50 border border-rose-200 text-rose-700 rounded-xl text-xs font-semibold">
                            {{ session('error') }}
                        </div>
                    @endif

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="form-label">Product Code <span class="text-rose-500">*</span></label>
                            <input wire:model="newProductCode" type="text" class="form-input" required />
                        </div>
                        <div>
                            <label class="form-label">Category</label>
                            <input wire:model="newProductCategory" type="text" class="form-input" placeholder="e.g. Electronics" />
                        </div>
                    </div>
                    
                    <div>
                        <label class="form-label">Item Name <span class="text-rose-500">*</span></label>
                        <input wire:model="newProductName" type="text" class="form-input" required />
                    </div>

                    <div class="grid grid-cols-3 gap-4">
                        <div>
                            <label class="form-label">Unit <span class="text-rose-500">*</span></label>
                            <select wire:model="newProductUnit" class="form-input" required>
                                <option value="Pcs">Pcs</option>
                                <option value="Kgs">Kgs</option>
                                <option value="Ltrs">Ltrs</option>
                                <option value="Packs">Packs</option>
                                <option value="Boxes">Boxes</option>
                            </select>
                        </div>
                        <div>
                            <label class="form-label">Buy Price <span class="text-rose-500">*</span></label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-500 text-sm font-medium">Rs</span>
                                <input wire:model="newProductBuyingPrice" type="number" step="0.01" class="form-input pl-9" required />
                            </div>
                        </div>
                        <div>
                            <label class="form-label">Sell Price <span class="text-rose-500">*</span></label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-500 text-sm font-medium">Rs</span>
                                <input wire:model="newProductSellingPrice" type="number" step="0.01" class="form-input pl-9" required />
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="form-label">Reorder Level</label>
                            <input wire:model="newProductReorderLevel" type="number" class="form-input" />
                            <p class="text-[10px] text-slate-400 mt-1">Alerts when stock drops below this</p>
                        </div>
                        <div>
                            <label class="form-label">Initial Stock Quantity</label>
                            <input wire:model="newProductQuantity" type="number" class="form-input" />
                            <p class="text-[10px] text-slate-400 mt-1">Initial quantity in stock</p>
                        </div>
                    </div>

                    {{-- Photo Upload --}}
                    <div>
                        <label class="form-label">Product Photo <span class="text-slate-400 font-normal">(optional)</span></label>
                        <div x-data="{ preview: null }"
                             x-on:livewire-upload-finish="preview = null">
                            <label class="flex flex-col items-center justify-center w-full h-28 border-2 border-dashed border-slate-200 rounded-xl cursor-pointer hover:border-primary/40 hover:bg-primary/5 transition-colors group">
                                <input type="file" wire:model="newProductPhoto" accept="image/*" class="hidden"
                                       x-on:change="preview = URL.createObjectURL($event.target.files[0])" />
                                <template x-if="preview">
                                    <img :src="preview" class="h-24 w-full object-contain rounded-xl p-1" />
                                </template>
                                <template x-if="!preview">
                                    <div class="flex flex-col items-center gap-1 text-slate-400 group-hover:text-primary transition-colors">
                                        <svg class="w-7 h-7" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5"/></svg>
                                        <span class="text-xs font-medium">Click to upload image</span>
                                        <span class="text-[10px]">PNG, JPG, WEBP up to 2 MB</span>
                                    </div>
                                </template>
                            </label>
                            <div wire:loading wire:target="newProductPhoto" class="mt-1 text-xs text-primary font-medium">Uploading...</div>
                            @error('newProductPhoto') <p class="text-[10px] text-rose-500 mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>
                
                <div class="px-6 py-4 border-t border-slate-100 bg-slate-50 rounded-b-2xl flex justify-end gap-3">
                    <button type="button" onclick="closeModal('addProductModal')" class="px-4 py-2 text-sm font-semibold text-slate-600 hover:text-slate-800 bg-white hover:bg-slate-100 border border-slate-200 rounded-xl transition-colors">
                        Cancel
                    </button>
                    <button type="submit" class="btn-primary" wire:loading.attr="disabled">
                        <span wire:loading.remove wire:target="saveProduct">Save Item</span>
                        <span wire:loading wire:target="saveProduct" class="flex items-center gap-2">
                            <svg class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                            Saving...
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Edit Product Modal --}}
    <div id="editProductModal" wire:ignore.self class="modal-overlay" onclick="if(event.target===this)closeModal('editProductModal')">
        <div class="modal-content">
            <form wire:submit="updateProduct">
                <div class="px-6 py-4 border-b border-slate-100 flex justify-between items-center bg-white rounded-t-2xl">
                    <h3 class="text-lg font-bold text-slate-800 tracking-tight">Edit Item</h3>
                    <button type="button" onclick="closeModal('editProductModal')" class="text-slate-400 hover:text-slate-600 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
                
                <div class="px-6 py-5 space-y-4 max-h-[70vh] overflow-y-auto">
                    <!-- Error messages -->
                    @if(session()->has('error'))
                        <div class="p-3 bg-rose-50 border border-rose-200 text-rose-700 rounded-xl text-xs font-semibold">
                            {{ session('error') }}
                        </div>
                    @endif

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="form-label">Product Code <span class="text-rose-500">*</span></label>
                            <input wire:model="editProductCode" type="text" class="form-input" required />
                        </div>
                        <div>
                            <label class="form-label">Category</label>
                            <input wire:model="editProductCategory" type="text" class="form-input" placeholder="e.g. Electronics" />
                        </div>
                    </div>
                    
                    <div>
                        <label class="form-label">Item Name <span class="text-rose-500">*</span></label>
                        <input wire:model="editProductName" type="text" class="form-input" required />
                    </div>

                    <div class="grid grid-cols-3 gap-4">
                        <div>
                            <label class="form-label">Unit <span class="text-rose-500">*</span></label>
                            <select wire:model="editProductUnit" class="form-input" required>
                                <option value="Pcs">Pcs</option>
                                <option value="Kgs">Kgs</option>
                                <option value="Ltrs">Ltrs</option>
                                <option value="Packs">Packs</option>
                                <option value="Boxes">Boxes</option>
                            </select>
                        </div>
                        <div>
                            <label class="form-label">Buy Price <span class="text-rose-500">*</span></label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-500 text-sm font-medium">Rs</span>
                                <input wire:model="editProductBuyingPrice" type="number" step="0.01" class="form-input pl-9" required />
                            </div>
                        </div>
                        <div>
                            <label class="form-label">Sell Price <span class="text-rose-500">*</span></label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-500 text-sm font-medium">Rs</span>
                                <input wire:model="editProductSellingPrice" type="number" step="0.01" class="form-input pl-9" required />
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="form-label">Reorder Level</label>
                            <input wire:model="editProductReorderLevel" type="number" class="form-input" />
                            <p class="text-[10px] text-slate-400 mt-1">Alerts when stock drops below this</p>
                        </div>
                        <div>
                            <label class="form-label">Stock Quantity</label>
                            <input wire:model="editProductQuantity" type="number" class="form-input" />
                            <p class="text-[10px] text-slate-400 mt-1">Current quantity in stock</p>
                        </div>
                    </div>

                    {{-- Photo Upload --}}
                    <div>
                        <label class="form-label">Product Photo <span class="text-slate-400 font-normal">(optional)</span></label>
                        <div x-data="{ preview: null }">
                            @if($editProductPhotoUrl && !$editProductPhoto)
                                <div class="mb-2 flex items-center gap-3">
                                    <img src="{{ $editProductPhotoUrl }}" alt="Current photo" class="w-14 h-14 object-cover rounded-xl border border-slate-200 shadow-sm" />
                                    <span class="text-[10px] text-slate-400">Current photo — upload a new one to replace it</span>
                                </div>
                            @endif
                            <label class="flex flex-col items-center justify-center w-full h-28 border-2 border-dashed border-slate-200 rounded-xl cursor-pointer hover:border-primary/40 hover:bg-primary/5 transition-colors group">
                                <input type="file" wire:model="editProductPhoto" accept="image/*" class="hidden"
                                       x-on:change="preview = URL.createObjectURL($event.target.files[0])" />
                                <template x-if="preview">
                                    <img :src="preview" class="h-24 w-full object-contain rounded-xl p-1" />
                                </template>
                                <template x-if="!preview">
                                    <div class="flex flex-col items-center gap-1 text-slate-400 group-hover:text-primary transition-colors">
                                        <svg class="w-7 h-7" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5"/></svg>
                                        <span class="text-xs font-medium">{{ $editProductPhotoUrl ? 'Upload to replace' : 'Click to upload image' }}</span>
                                        <span class="text-[10px]">PNG, JPG, WEBP up to 2 MB</span>
                                    </div>
                                </template>
                            </label>
                            <div wire:loading wire:target="editProductPhoto" class="mt-1 text-xs text-primary font-medium">Uploading...</div>
                            @error('editProductPhoto') <p class="text-[10px] text-rose-500 mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>
                
                <div class="px-6 py-4 border-t border-slate-100 bg-slate-50 rounded-b-2xl flex justify-end gap-3">
                    <button type="button" onclick="closeModal('editProductModal')" class="px-4 py-2 text-sm font-semibold text-slate-600 hover:text-slate-800 bg-white hover:bg-slate-100 border border-slate-200 rounded-xl transition-colors">
                        Cancel
                    </button>
                    <button type="submit" class="btn-primary" wire:loading.attr="disabled">
                        <span wire:loading.remove wire:target="updateProduct">Update Item</span>
                        <span wire:loading wire:target="updateProduct" class="flex items-center gap-2">
                            <svg class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                            Updating...
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    @script
    <script>
        $wire.on('open-modal-js', (event) => {
            const id = (event && event.modalId) || (event && event[0] && event[0].modalId) || (Array.isArray(event) ? event[0] : event);
            window.openModal(id);
        });
        
        $wire.on('close-modal-js', (event) => {
            const id = (event && event.modalId) || (event && event[0] && event[0].modalId) || (Array.isArray(event) ? event[0] : event);
            window.closeModal(id);
            window.showToast(id === 'editProductModal' ? 'Product updated successfully!' : 'Product added successfully!');
        });

        // Re-apply admin styles after Livewire updates because Livewire restores display:none
        document.addEventListener('livewire:initialized', () => {
            Livewire.hook('morph.updated', ({ el, component }) => {
                const user = window.getCurrentUser ? window.getCurrentUser() : null;
                if (user && user.role === 'Admin') {
                    document.querySelectorAll('.admin-only').forEach(element => {
                        if (element.tagName === 'TH' || element.tagName === 'TD') {
                            element.style.display = 'table-cell';
                        } else if (element.classList.contains('flex') || element.classList.contains('inline-flex')) {
                            element.style.display = 'flex';
                        } else {
                            element.style.display = 'block';
                        }
                    });
                }
            });
        });
    </script>
    @endscript
</div>
