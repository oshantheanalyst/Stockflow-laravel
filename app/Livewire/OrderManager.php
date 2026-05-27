<?php

namespace App\Livewire;

use App\Models\Customer;
use App\Models\Invoice;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use App\Traits\DispatchesApiRequests;
use Livewire\Component;

class OrderManager extends Component
{
    use DispatchesApiRequests;

    public $invoice_no = '';
    public $invoice_date = '';
    public $customer_id = '';
    public $phone = '';
    public $location = '';

    public $showNewCustomerModal = false;
    public $new_customer_name = '';
    public $new_customer_phone = '';
    public $new_customer_area = '';

    public $payment_method = 'Cash';
    public $credit_days = 30;
    public $cheque_bank_name = '';
    public $cheque_number = '';
    public $cheque_due_date = '';

    public $selected_product = '';
    public $line_qty = 1;
    public $line_price = 0.00;

    public $items = [];
    public $discount = 0.00;

    public $subtotal = 0.00;
    public $net_total = 0.00;

    public $products = [];
    public $customers = [];

    public function mount()
    {
        $this->invoice_date = date('Y-m-d');
        $this->cheque_due_date = date('Y-m-d');
        $this->loadFormData();
    }

    public function updatedCustomerId($value)
    {
        $customer = collect($this->customers)->firstWhere('id', $value);

        if ($customer) {
            $this->phone = $customer['phone'] ?? '—';
            $this->location = $customer['area'] ?? '—';
        } else {
            $this->phone = '';
            $this->location = '';
        }
    }

    public function updatedSelectedProduct($value)
    {
        $product = collect($this->products)->firstWhere('id', $value);

        if ($product) {
            $this->line_price = $product['selling_price'];
        } else {
            $this->line_price = 0.00;
        }
    }

    public function addItem()
    {
        $this->validate([
            'selected_product' => 'required',
            'line_qty'         => 'required|numeric|min:1',
            'line_price'       => 'required|numeric|min:0',
        ]);

        $product = collect($this->products)->firstWhere('id', $this->selected_product);

        if (! $product) {
            session()->flash('error', 'Selected product was not found.');
            return;
        }

        if (($product['current_stock'] ?? 0) < $this->line_qty) {
            session()->flash('error', 'Insufficient stock for ' . $product['name'] . '. Available: ' . (int)($product['current_stock'] ?? 0));
            return;
        }

        foreach ($this->items as $index => $item) {
            if ($item['product_id'] == $this->selected_product) {
                $this->items[$index]['qty'] = $this->items[$index]['qty'] + $this->line_qty;
                $this->items[$index]['line_total'] = $this->items[$index]['qty'] * $this->items[$index]['unit_price'];
                $this->recalculate();
                $this->resetProductInputs();
                return;
            }
        }

        $this->items[] = [
            'product_id'   => $product['id'],
            'name'         => $product['name'],
            'qty'          => $this->line_qty,
            'unit_price'   => $this->line_price,
            'buying_price' => $product['buying_price'],
            'line_total'   => $this->line_qty * $this->line_price,
        ];

        $this->recalculate();
        $this->resetProductInputs();
    }

    public function removeItem($index)
    {
        unset($this->items[$index]);
        $this->items = array_values($this->items);
        $this->recalculate();
    }

    public function resetProductInputs()
    {
        $this->selected_product = '';
        $this->line_qty = 1;
        $this->line_price = 0.00;
    }

    public function recalculate()
    {
        $this->subtotal = array_sum(array_column($this->items, 'line_total'));
        $this->net_total = $this->subtotal - (float)$this->discount;
    }

    public function updatedDiscount()
    {
        $this->recalculate();
    }

    public function openNewCustomerModal()
    {
        $this->new_customer_name = '';
        $this->new_customer_phone = '';
        $this->new_customer_area = '';
        $this->showNewCustomerModal = true;
    }

    public function closeNewCustomerModal()
    {
        $this->showNewCustomerModal = false;
    }

    public function saveNewCustomer()
    {
        $this->validate([
            'new_customer_name' => 'required|string|min:2|max:255',
            'new_customer_phone' => 'nullable|string|max:50',
            'new_customer_area' => 'nullable|string|max:255',
        ]);

        try {
            $nextId = Customer::withoutGlobalScopes()->max('id') + 1;
            $customerCode = 'C' . str_pad($nextId, 3, '0', STR_PAD_LEFT);

            $customer = Customer::create([
                'customer_code' => $customerCode,
                'name' => $this->new_customer_name,
                'phone' => $this->new_customer_phone,
                'area' => $this->new_customer_area,
                'is_active' => true,
            ]);

            $newCustomer = $customer->toArray();
            $this->customers[] = $newCustomer;

            $this->customer_id = $newCustomer['id'];
            $this->updatedCustomerId($newCustomer['id']);

            $this->closeNewCustomerModal();
            session()->flash('message', 'New customer "' . $customer->name . '" created successfully!');
        } catch (\Exception $e) {
            \Log::error('saveNewCustomer failed: ' . $e->getMessage());
            $this->addError('new_customer_name', 'Failed to save: ' . $e->getMessage());
        }
    }

    public function saveInvoice()
    {
        if (empty($this->items)) {
            session()->flash('error', 'Please add at least one item to the invoice.');
            return;
        }

        $this->validate([
            'invoice_no'     => 'required|string|unique:orders,invoice_no',
            'invoice_date'   => 'required|date',
            'payment_method' => 'required',
        ]);

        try {
            DB::transaction(function () {
                if ($this->customer_id) {
                    Customer::where('id', $this->customer_id)->update([
                        'phone' => $this->phone,
                        'area'  => $this->location,
                    ]);
                }

                $subtotal = 0;
                $discount = $this->discount ?: 0;
                $itemsData = [];

                foreach ($this->items as $item) {
                    $product = Product::findOrFail($item['product_id']);
                    $lineTotal = $item['qty'] * $item['unit_price'];
                    $subtotal += $lineTotal;
                    $itemsData[] = [
                        'product'       => $product,
                        'qty'           => $item['qty'],
                        'unit_price'    => $item['unit_price'],
                        'line_total'    => $lineTotal,
                    ];
                }

                $total = $subtotal - $discount;
                $isPaid = $this->payment_method === 'Cash';

                $dueDate = null;
                if ($this->payment_method === 'Credit' && $this->credit_days) {
                    $dueDate = date('Y-m-d', strtotime($this->invoice_date . ' + ' . $this->credit_days . ' days'));
                } elseif ($this->payment_method === 'Cheque') {
                    $dueDate = $this->cheque_due_date ?: null;
                }

                $invoice = Invoice::create([
                    'invoice_no'         => $this->invoice_no,
                    'customer_id'        => $this->customer_id ?: null,
                    'invoice_date'       => $this->invoice_date,
                    'subtotal'           => $subtotal,
                    'discount'           => $discount,
                    'total'              => $total,
                    'amount_paid'        => $isPaid ? $total : 0,
                    'payment_method'     => $this->payment_method,
                    'is_paid'            => $isPaid,
                    'due_date'           => $dueDate,
                    'credit_period_days' => $this->payment_method === 'Credit' ? $this->credit_days : null,
                    'cheque_bank_name'   => $this->payment_method === 'Cheque' ? $this->cheque_bank_name : null,
                    'cheque_number'      => $this->payment_method === 'Cheque' ? $this->cheque_number : null,
                    'is_deleted'         => false,
                ]);

                foreach ($itemsData as $item) {
                    $invoice->items()->create([
                        'product_id'             => $item['product']->id,
                        'qty'                    => $item['qty'],
                        'unit_price_snapshot'    => $item['unit_price'],
                        'buying_price_snapshot'  => $item['product']->buying_price,
                        'line_total'             => $item['line_total'],
                    ]);

                    Product::where('id', $item['product']->id)->decrement('current_stock', $item['qty']);
                }
            });

            session()->flash('message', 'Invoice created successfully!');
            return redirect()->route('sales.index');

        } catch (\Exception $e) {
            \Log::error('saveInvoice failed: ' . $e->getMessage());
            session()->flash('error', 'Unable to create the invoice: ' . $e->getMessage());
        }
    }

    protected function loadFormData()
    {
        $response = $this->apiGet('/sales/create-form');

        if (! $response->ok) {
            session()->flash('error', 'Unable to load order form data.');
            return;
        }

        $payload = $response->payload['data'] ?? [];
        $this->invoice_no = $payload['next_invoice_no'] ?? '';
        $this->products = collect($payload['products'] ?? [])->map(function ($item) {
            return is_object($item) ? (array) $item : $item;
        })->all();

        $this->customers = collect($payload['customers'] ?? [])->map(function ($item) {
            return is_object($item) ? (array) $item : $item;
        })->all();
    }

    public function render()
    {
        return view('livewire.order-manager', [
            'products'  => $this->products,
            'customers' => $this->customers,
        ]);
    }
}
