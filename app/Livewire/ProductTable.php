<?php

namespace App\Livewire;

use App\Traits\DispatchesApiRequests;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class ProductTable extends Component
{
    use WithPagination;
    use WithFileUploads;
    use DispatchesApiRequests;

    public $search = '';
    public $categoryFilter = '';
    public $page = 1;

    public $totalProducts = 0;
    public $lowStockCount = 0;
    public $totalStockQty = 0;
    public $stockValuation = 0.0;
    public $categories = [];
    public $productList = [];

    protected $queryString = [
        'search'         => ['except' => ''],
        'categoryFilter' => ['except' => ''],
        'page'           => ['except' => 1],
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingCategoryFilter()
    {
        $this->resetPage();
    }

    public function toggleActive($productId)
    {
        if (auth()->user()->role !== 'Admin') {
            session()->flash('error', 'Unauthorized. Only Admins can modify products.');
            return;
        }

        try {
            $product = \App\Models\Product::findOrFail($productId);
            $product->update([
                'is_active' => !$product->is_active
            ]);
            session()->flash('message', 'Product status updated successfully.');
        } catch (\Exception $e) {
            \Log::error('toggleActive failed: ' . $e->getMessage());
            session()->flash('error', 'Unable to update product status.');
        }

        $this->resetPage();
    }

    // Modal Form Properties for Adding
    public $newProductCode = '';
    public $newProductName = '';
    public $newProductCategory = '';
    public $newProductUnit = 'Pcs';
    public $newProductBuyingPrice = '';
    public $newProductSellingPrice = '';
    public $newProductReorderLevel = '';
    public $newProductQuantity = 0;
    public $newProductPhoto = null; // optional file upload

    // Modal Form Properties for Editing
    public $editingProductId = null;
    public $editProductCode = '';
    public $editProductName = '';
    public $editProductCategory = '';
    public $editProductUnit = 'Pcs';
    public $editProductBuyingPrice = '';
    public $editProductSellingPrice = '';
    public $editProductReorderLevel = '';
    public $editProductQuantity = 0;
    public $editProductPhoto = null;      // optional new file upload
    public $editProductPhotoUrl = null;   // existing photo URL for preview

    public function mount()
    {
        // Set an initial product code based on total products or via API, but we'll fetch it properly in render
    }

    public function openAddModal()
    {
        if (auth()->user()->role !== 'Admin') {
            session()->flash('error', 'Unauthorized. Only Admins can add products.');
            return;
        }
        $this->reset(['newProductName', 'newProductCategory', 'newProductBuyingPrice', 'newProductSellingPrice', 'newProductReorderLevel', 'newProductQuantity', 'newProductPhoto']);
        $this->newProductUnit = 'Pcs';
        $this->dispatch('open-modal-js', modalId: 'addProductModal'); 
    }

    public function saveProduct()
    {
        if (auth()->user()->role !== 'Admin') {
            session()->flash('error', 'Unauthorized. Only Admins can add products.');
            return;
        }

        $this->validate([
            'newProductCode'          => 'required|string|unique:products,product_code',
            'newProductName'          => 'required|string|max:255',
            'newProductBuyingPrice'   => 'required|numeric|min:0',
            'newProductSellingPrice'  => 'required|numeric|min:0',
            'newProductPhoto'         => 'nullable|image|max:2048', // max 2 MB
        ], [
            'newProductCode.required'         => 'The product code field is required.',
            'newProductName.required'         => 'The item name field is required.',
            'newProductBuyingPrice.required'  => 'The buy price field is required.',
            'newProductSellingPrice.required' => 'The sell price field is required.',
            'newProductPhoto.image'           => 'The photo must be an image file.',
            'newProductPhoto.max'             => 'The photo may not be larger than 2 MB.',
        ]);

        try {
            $photoPath = null;
            if ($this->newProductPhoto) {
                $photoPath = $this->newProductPhoto->store('products/photos', 'public');
            }

            \App\Models\Product::create([
                'product_code'   => $this->newProductCode,
                'name'           => $this->newProductName,
                'category'       => $this->newProductCategory,
                'unit'           => $this->newProductUnit,
                'buying_price'   => (float) $this->newProductBuyingPrice,
                'selling_price'  => (float) $this->newProductSellingPrice,
                'reorder_level'  => (int) $this->newProductReorderLevel,
                'is_active'      => true,
                'current_stock'  => (float) $this->newProductQuantity,
                'photo_path'     => $photoPath,
            ]);

            session()->flash('message', 'Product added successfully.');
            $this->dispatch('close-modal-js', modalId: 'addProductModal');
            $this->reset(['newProductName', 'newProductCategory', 'newProductBuyingPrice', 'newProductSellingPrice', 'newProductReorderLevel', 'newProductQuantity', 'newProductPhoto']);
            
            $nextId = \App\Models\Product::max('id') + 1;
            $this->newProductCode = 'P' . str_pad($nextId, 3, '0', STR_PAD_LEFT);
            
            $this->resetPage();
        } catch (\Exception $e) {
            \Log::error('saveProduct failed: ' . $e->getMessage());
            session()->flash('error', 'Error adding product: ' . $e->getMessage());
        }
    }

    public function openEditModal($productId)
    {
        if (auth()->user()->role !== 'Admin') {
            session()->flash('error', 'Unauthorized. Only Admins can edit products.');
            return;
        }

        try {
            $product = \App\Models\Product::findOrFail($productId);
            $this->editingProductId      = $product->id;
            $this->editProductCode       = $product->product_code;
            $this->editProductName       = $product->name;
            $this->editProductCategory   = $product->category;
            $this->editProductUnit       = $product->unit ?? 'Pcs';
            $this->editProductBuyingPrice   = $product->buying_price;
            $this->editProductSellingPrice  = $product->selling_price;
            $this->editProductReorderLevel  = $product->reorder_level;
            $this->editProductQuantity   = $product->current_stock;
            $this->editProductPhoto      = null; // reset upload input
            $this->editProductPhotoUrl   = $product->photo_url; // existing URL for preview

            $this->dispatch('open-modal-js', modalId: 'editProductModal');
        } catch (\Exception $e) {
            session()->flash('error', 'Product not found.');
        }
    }

    public function updateProduct()
    {
        if (auth()->user()->role !== 'Admin') {
            session()->flash('error', 'Unauthorized. Only Admins can modify products.');
            return;
        }

        $this->validate([
            'editProductCode'         => 'required|string|unique:products,product_code,' . $this->editingProductId,
            'editProductName'         => 'required|string|max:255',
            'editProductBuyingPrice'  => 'required|numeric|min:0',
            'editProductSellingPrice' => 'required|numeric|min:0',
            'editProductPhoto'        => 'nullable|image|max:2048',
        ], [
            'editProductCode.required'         => 'The product code field is required.',
            'editProductName.required'         => 'The item name field is required.',
            'editProductBuyingPrice.required'  => 'The buy price field is required.',
            'editProductSellingPrice.required' => 'The sell price field is required.',
            'editProductPhoto.image'           => 'The photo must be an image file.',
            'editProductPhoto.max'             => 'The photo may not be larger than 2 MB.',
        ]);

        try {
            $product = \App\Models\Product::findOrFail($this->editingProductId);

            $updateData = [
                'product_code'  => $this->editProductCode,
                'name'          => $this->editProductName,
                'category'      => $this->editProductCategory,
                'unit'          => $this->editProductUnit,
                'buying_price'  => (float) $this->editProductBuyingPrice,
                'selling_price' => (float) $this->editProductSellingPrice,
                'reorder_level' => (int) $this->editProductReorderLevel,
                'current_stock' => (float) $this->editProductQuantity,
            ];

            if ($this->editProductPhoto) {
                // Delete old photo if it exists
                if ($product->photo_path) {
                    \Illuminate\Support\Facades\Storage::disk('public')->delete($product->photo_path);
                }
                $updateData['photo_path'] = $this->editProductPhoto->store('products/photos', 'public');
            }

            $product->update($updateData);

            session()->flash('message', 'Product updated successfully.');
            $this->dispatch('close-modal-js', modalId: 'editProductModal');
            $this->reset(['editingProductId', 'editProductCode', 'editProductName', 'editProductCategory', 'editProductBuyingPrice', 'editProductSellingPrice', 'editProductReorderLevel', 'editProductQuantity', 'editProductPhoto', 'editProductPhotoUrl']);
            $this->resetPage();
        } catch (\Exception $e) {
            \Log::error('updateProduct failed: ' . $e->getMessage());
            session()->flash('error', 'Error updating product: ' . $e->getMessage());
        }
    }

    public function render()
    {
        $productsResponse = $this->apiGet('/products', [
            'search' => $this->search,
            'category_id' => $this->categoryFilter,
        ]);

        $products = collect($productsResponse->payload['data'] ?? [])->map(function ($item) {
            return is_array($item) ? (object) $item : $item;
        });

        $this->productList = $products->all();
        $this->totalProducts = $productsResponse->payload['meta']['total_items'] ?? $products->count();
        
        if (empty($this->newProductCode)) {
            $this->newProductCode = $productsResponse->payload['next_code'] ?? '';
        }
        $this->lowStockCount = $productsResponse->payload['meta']['low_stock_count'] ?? $products->filter(function ($item) {
            return isset($item->current_stock, $item->reorder_level) && $item->current_stock <= $item->reorder_level;
        })->count();
        $this->totalStockQty = $productsResponse->payload['meta']['total_stock_qty'] ?? $products->sum(fn ($item) => $item->current_stock ?? 0);
        $this->stockValuation = $productsResponse->payload['meta']['total_stock_val'] ?? 0;

        $categoriesResponse = $this->apiGet('/categories');
        $this->categories = collect($categoriesResponse->payload['data'] ?? [])->map(function ($item) {
            return is_array($item) ? (object) $item : $item;
        })->all();

        $perPage = 10;
        $currentPage = max(1, (int) $this->page);
        $pagedItems = $products->slice(($currentPage - 1) * $perPage, $perPage)->values();

        $productsPaginator = new LengthAwarePaginator(
            $pagedItems,
            $products->count(),
            $perPage,
            $currentPage,
            [
                'path' => Paginator::resolveCurrentPath(),
                'pageName' => 'page',
            ]
        );

        return view('livewire.product-table', [
            'productResults' => $productsPaginator,
            'categories' => $this->categories,
        ]);
    }
}
