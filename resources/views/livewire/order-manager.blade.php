<div>
    @if(session()->has('error'))
        <div class="mb-4 p-4 bg-rose-50 border border-rose-200 text-rose-800 rounded-xl text-xs font-semibold">
            {{ session('error') }}
        </div>
    @endif

    {{-- Invoice Details --}}
    <div class="card mb-6">
        <h3 class="text-sm font-bold text-slate-800 uppercase tracking-wider mb-4 pb-2 border-b border-slate-100">1. Invoice Details</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
            <div>
                <label class="form-label">Invoice No *</label>
                <input wire:model="invoice_no" class="form-input font-mono font-semibold text-primary" required />
                @error('invoice_no') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
            </div>
            <div>
                <label class="form-label">Date *</label>
                <input wire:model="invoice_date" type="date" class="form-input" required />
                @error('invoice_date') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
            </div>
            <div>
                <label class="form-label">Customer</label>
                <div class="flex items-center gap-2">
                    <div x-data="{
                        open: false,
                        search: '',
                        selectedId: @entangle('customer_id').live,
                        get customers() { return $wire.customers || []; },
                        get filteredCustomers() {
                            if (!this.customers || !Array.isArray(this.customers)) return [];
                            if (this.search === '') return this.customers;
                            return this.customers.filter(c => c && c.name && (c.name.toLowerCase().includes(this.search.toLowerCase()) || (c.phone && c.phone.includes(this.search))));
                        },
                        selectCustomer(id, name) {
                            this.selectedId = id;
                            this.search = name;
                            this.open = false;
                        },
                        init() {
                            if (this.selectedId && this.customers && Array.isArray(this.customers)) {
                                let c = this.customers.find(c => c && c.id == this.selectedId);
                                if (c) this.search = c.name;
                            }
                            this.$watch('selectedId', value => {
                                if (!value) {
                                    this.search = '';
                                } else if (this.customers && Array.isArray(this.customers)) {
                                    let c = this.customers.find(c => c && c.id == value);
                                    if (c) this.search = c.name;
                                }
                            });
                        }
                    }" class="relative flex-1">
                        <input type="text" x-model="search" @click="open = true" @click.outside="open = false" class="form-input w-full" placeholder="Search customer (name or phone)..." />
                        
                        <div wire:ignore x-show="open" x-transition style="display: none;" class="absolute z-10 w-full mt-1 bg-white border border-slate-200 rounded-xl shadow-lg max-h-60 overflow-y-auto">
                            <div @click="selectCustomer('', '')" class="px-4 py-2 hover:bg-slate-50 cursor-pointer text-slate-800 font-medium">Walk-in Customer</div>
                            <template x-for="c in filteredCustomers" :key="c.id">
                                <div @click="selectCustomer(c.id, c.name)" class="px-4 py-2 hover:bg-slate-50 cursor-pointer border-t border-slate-50">
                                    <div class="font-bold text-slate-800" x-text="c.name"></div>
                                    <div class="text-xs text-slate-500" x-show="c.phone" x-text="c.phone"></div>
                                </div>
                            </template>
                            <div x-show="filteredCustomers.length === 0" class="px-4 py-3 text-slate-500 text-sm">No customers found.</div>
                        </div>
                    </div>
                    <button type="button" wire:click="openNewCustomerModal" class="p-2.5 bg-indigo-50 hover:bg-indigo-100 text-indigo-600 rounded-xl border border-indigo-100 transition-colors shrink-0" title="Add New Customer">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                    </button>
                </div>
            </div>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="form-label">Phone</label>
                <input wire:model="phone" class="form-input" placeholder="Update phone..." />
            </div>
            <div>
                <label class="form-label">Location</label>
                <input wire:model="location" class="form-input" placeholder="Update location..." />
            </div>
        </div>
    </div>

    {{-- Payment Details --}}
    <div class="card mb-6">
        <h3 class="text-sm font-bold text-slate-800 uppercase tracking-wider mb-4 pb-2 border-b border-slate-100">2. Payment Details</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="form-label">Payment Method *</label>
                <select wire:model.live="payment_method" class="form-select">
                    <option value="Cash">Cash</option>
                    <option value="Credit">Credit</option>
                    <option value="Cheque">Cheque</option>
                </select>
            </div>
        </div>
        
        @if($payment_method === 'Credit')
            <div class="mt-4">
                <label class="form-label">Credit Terms (Days)</label>
                <input wire:model="credit_days" type="number" class="form-input w-32" />
            </div>
        @endif

        @if($payment_method === 'Cheque')
            <div class="mt-4 grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="form-label">Bank Name</label>
                    <input wire:model="cheque_bank_name" class="form-input" placeholder="e.g. NTB, HNB" />
                </div>
                <div>
                    <label class="form-label">Cheque No</label>
                    <input wire:model="cheque_number" class="form-input" placeholder="Cheque reference" />
                </div>
                <div>
                    <label class="form-label">Cheque Due Date</label>
                    <input wire:model="cheque_due_date" type="date" class="form-input" />
                </div>
            </div>
        @endif
    </div>

    {{-- Line Items --}}
    <div class="card mb-6">
        <h3 class="text-sm font-bold text-slate-800 uppercase tracking-wider mb-4 pb-2 border-b border-slate-100">3. Select and Add Items</h3>
        <div class="bg-slate-50 rounded-2xl border border-slate-100 p-4 mb-4">
            <div class="grid grid-cols-12 gap-3 items-end">
                <div class="col-span-12 md:col-span-5">
                    <label class="form-label">Item / Product</label>
                    <select wire:model.live="selected_product" class="form-select">
                        <option value="">Choose item...</option>
                        @foreach($products as $p)
                            <option value="{{ $p['id'] }}">{{ $p['name'] }} (Stock: {{ (int)$p['current_stock'] }})</option>
                        @endforeach
                    </select>
                    @error('selected_product') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                </div>
                <div class="col-span-6 md:col-span-2">
                    <label class="form-label">Quantity</label>
                    <input wire:model="line_qty" type="number" min="1" class="form-input" placeholder="Qty" />
                    @error('line_qty') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                </div>
                <div class="col-span-6 md:col-span-3">
                    <label class="form-label">Price per Unit (Rs)</label>
                    <input wire:model="line_price" type="number" step="0.01" class="form-input" placeholder="Rs 0.00" />
                    @error('line_price') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                </div>
                <div class="col-span-12 md:col-span-2">
                    <button type="button" wire:click="addItem" class="btn-primary w-full flex items-center justify-center gap-1.5 h-[44px] cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                        <span>Add</span>
                    </button>
                </div>
            </div>
        </div>
        
        <table class="data-table">
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Qty</th>
                    <th>Unit Price</th>
                    <th>Line Total</th>
                    <th class="w-20"></th>
                </tr>
            </thead>
            <tbody>
                @forelse($items as $index => $item)
                    <tr>
                        <td class="font-bold text-slate-800">{{ $item['name'] }}</td>
                        <td class="font-semibold text-slate-600">{{ $item['qty'] }}</td>
                        <td class="font-medium text-slate-500">Rs {{ number_format($item['unit_price'], 2) }}</td>
                        <td class="font-bold text-slate-900">Rs {{ number_format($item['line_total'], 2) }}</td>
                        <td>
                            <button type="button" wire:click="removeItem({{ $index }})" class="p-1.5 bg-rose-50 hover:bg-rose-500/10 text-rose-500 rounded-lg border border-rose-100/50 cursor-pointer">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center py-10 text-text-secondary">No items added yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Totals & Submit --}}
    <div class="card mb-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-end gap-6">
            <div class="flex items-center gap-3">
                <label class="font-bold text-slate-500 text-xs uppercase tracking-wider">Discount (Rs)</label>
                <input wire:model.live="discount" type="number" step="0.01" class="form-input w-36 font-semibold" />
            </div>
            <div class="flex items-center gap-3 border-l border-slate-200 pl-6 h-10">
                <span class="font-bold text-slate-500 text-xs uppercase tracking-wider">Net Total</span>
                <span class="font-extrabold text-2xl text-primary">Rs {{ number_format($net_total, 2) }}</span>
            </div>
        </div>
    </div>

    <div class="flex justify-end gap-3 pb-12">
        <a href="/sales" class="btn-ghost px-8">Cancel</a>
        <button type="button" wire:click="saveInvoice" class="btn-primary px-8 cursor-pointer" {{ empty($items) ? 'disabled' : '' }}>
            Save Invoice
        </button>
    </div>

    {{-- New Customer Modal --}}
    <div class="modal-overlay {{ $showNewCustomerModal ? 'active' : '' }}">
        <div class="modal-content">
            <form wire:submit.prevent="saveNewCustomer">
                <div class="px-6 py-4 border-b border-border flex items-center justify-between">
                    <h3 class="text-lg font-bold">Add New Customer</h3>
                    <button type="button" wire:click="closeNewCustomerModal" class="text-slate-400 hover:text-slate-600 font-bold text-xl cursor-pointer">&times;</button>
                </div>
                <div class="px-6 py-4 space-y-3">
                    <div>
                        <label class="form-label">Customer Name *</label>
                        <input wire:model="new_customer_name" class="form-input" required />
                        @error('new_customer_name') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="form-label">Phone</label>
                        <input wire:model="new_customer_phone" class="form-input" />
                        @error('new_customer_phone') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="form-label">Area / Location</label>
                        <input wire:model="new_customer_area" class="form-input" />
                        @error('new_customer_area') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>
                </div>
                <div class="px-6 py-4 border-t border-border flex justify-end gap-2">
                    <button type="button" wire:click="closeNewCustomerModal" class="btn-ghost cursor-pointer">Cancel</button>
                    <button type="submit" class="btn-primary cursor-pointer">Save Customer</button>
                </div>
            </form>
        </div>
    </div>
</div>
