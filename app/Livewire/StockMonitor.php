<?php

namespace App\Livewire;

use App\Traits\DispatchesApiRequests;
use Livewire\Component;

class StockMonitor extends Component
{
    use DispatchesApiRequests;

    public $search = '';
    public $lowStockProducts = [];

    public function render()
    {
        $response = $this->apiGet('/products', [
            'low_stock' => true,
            'search'    => $this->search,
        ]);

        $this->lowStockProducts = collect($response->payload['data'] ?? [])->map(function ($item) {
            return is_array($item) ? (object) $item : $item;
        })->all();

        return view('livewire.stock-monitor', [
            'lowStockProducts' => $this->lowStockProducts,
        ]);
    }
}
