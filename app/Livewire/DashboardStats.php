<?php

namespace App\Livewire;

use App\Traits\DispatchesApiRequests;
use Livewire\Component;

class DashboardStats extends Component
{
    use DispatchesApiRequests;

    public $totalSales = 0;
    public $ordersCount = 0;
    public $productsCount = 0;
    public $avgOrderValue = 0;

    public function render()
    {
        $response = $this->apiGet('/reports');

        if ($response->ok && isset($response->payload['data'])) {
            $stats = $response->payload['data'];
            $this->totalSales = $stats['total_sales'] ?? 0;
            $this->ordersCount = $stats['invoice_count'] ?? 0;
            $this->productsCount = count($stats['top_products'] ?? []);
            $this->avgOrderValue = $this->ordersCount > 0 ? ($this->totalSales / $this->ordersCount) : 0;
        }

        return view('livewire.dashboard-stats', [
            'totalSales'    => $this->totalSales,
            'ordersCount'   => $this->ordersCount,
            'productsCount' => $this->productsCount,
            'avgOrderValue' => $this->avgOrderValue,
        ]);
    }
}
