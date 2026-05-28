<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Expense;
use App\Models\Invoice;
use App\Models\InvoiceItem;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ReportController extends Controller
{
    // Generate business report data for a given date range
    public function index(Request $request)
    {
        $startDate = $request->get('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', now()->format('Y-m-d'));
        $from = $startDate;
        $to = date('Y-m-d', strtotime($endDate . ' +1 day'));

        $invoices = Invoice::where('is_deleted', false)
            ->where('invoice_date', '>=', $from)->where('invoice_date', '<', $to)->get();
        $totalSales = $invoices->sum('total');
        $totalDiscount = $invoices->sum('discount');
        $invoiceCount = $invoices->count();

        $expenses = Expense::where('date', '>=', $from)->where('date', '<=', $endDate)->get();
        $totalExpenses = $expenses->sum('amount');

        $items = InvoiceItem::with(['invoice', 'product'])
            ->whereHas('invoice', fn ($q) => $q->where('is_deleted', false)->where('invoice_date', '>=', $from)->where('invoice_date', '<', $to))
            ->get();


        $totalCogs = $items->sum(fn ($ii) => $ii->qty * $ii->buying_price_snapshot);

        $productStats = [];
        foreach ($items->groupBy('product_id') as $pid => $g) {
            $productStats[$pid] = [
                'name' => $g->first()->product->name ?? 'Unknown',
                'qty_sold' => $g->sum('qty'),
                'revenue' => $g->sum('line_total'),
                'cogs' => $g->sum(fn ($ii) => $ii->qty * $ii->buying_price_snapshot),
            ];
        }

        $returnedCogs = 0;

        $netProfit = $totalSales - ($totalCogs - $returnedCogs) - $totalExpenses;
        $topProducts = collect($productStats)
            ->filter(fn ($p) => $p['qty_sold'] > 0)
            ->map(fn ($p) => array_merge($p, ['profit' => $p['revenue'] - $p['cogs']]))
            ->sortByDesc('revenue')
            ->take(10)
            ->values();
        $expensesByCategory = $expenses->groupBy('category')
            ->map(fn ($g, $c) => ['category' => $c, 'total' => $g->sum('amount')])
            ->sortByDesc('total')
            ->values();
        $dateRangeLabel = date('d M Y', strtotime($startDate)) . ' – ' . date('d M Y', strtotime($endDate));

        $exchangeRates = null;
        try {
            $response = Http::timeout(3)->get('https://api.exchangerate-api.com/v4/latest/LKR');
            if ($response->successful()) {
                $rates = $response->json()['rates'];
                $exchangeRates = [
                    'USD' => isset($rates['USD']) && $rates['USD'] > 0 ? (1 / $rates['USD']) : 300.00,
                    'AUD' => isset($rates['AUD']) && $rates['AUD'] > 0 ? (1 / $rates['AUD']) : 195.00,
                    'EUR' => isset($rates['EUR']) && $rates['EUR'] > 0 ? (1 / $rates['EUR']) : 325.00,
                ];
            }
        } catch (\Exception $e) {
            $exchangeRates = ['USD' => 300.00, 'AUD' => 195.00, 'EUR' => 325.00];
        }

        return response()->json([
            'success' => true,
            'message' => 'Report generated successfully.',
            'data' => [
                'start_date' => $startDate,
                'end_date' => $endDate,
                'date_range_label' => $dateRangeLabel,
                'total_sales' => $totalSales,
                'total_discount' => $totalDiscount,
                'total_expenses' => $totalExpenses,
                'net_profit' => $netProfit,
                'invoice_count' => $invoiceCount,
                'top_products' => $topProducts,
                'expenses_by_category' => $expensesByCategory,
                'exchange_rates' => $exchangeRates,
            ],
        ], 200);
    }
}
