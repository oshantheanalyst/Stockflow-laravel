<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class CurrencyController extends Controller
{
    // Free ExchangeRate-API (no key required for latest endpoint)
    private const EXCHANGE_API = 'https://open.er-api.com/v6/latest/';

    // Fetch live exchange rates for a given base currency (default USD)
    public function rates(Request $request)
    {
        $base = strtoupper($request->query('base', 'USD'));

        $cacheKey = "exchange_rates_{$base}";

        $data = Cache::remember($cacheKey, now()->addHour(), function () use ($base) {
            $response = Http::timeout(10)->get(self::EXCHANGE_API . $base);

            if ($response->failed()) {
                return null;
            }

            return $response->json();
        });

        if (! $data) {
            return response()->json([
                'success' => false,
                'message' => 'Unable to fetch exchange rates. Please try again later.',
            ], 503);
        }

        return response()->json([
            'success'      => true,
            'message'      => "Exchange rates fetched for base currency: {$base}",
            'base'         => $data['base_code'] ?? $base,
            'last_updated' => $data['time_last_update_utc'] ?? now()->toISOString(),
            'rates'        => $data['rates'] ?? [],
        ], 200);
    }

    // Convert an amount from one currency to another using cached rates
    public function convert(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0',
            'from'   => 'required|string|size:3',
            'to'     => 'required|string|size:3',
        ]);

        $from   = strtoupper($request->from);
        $to     = strtoupper($request->to);
        $amount = (float) $request->amount;

        $cacheKey = "exchange_rates_{$from}";

        $data = Cache::remember($cacheKey, now()->addHour(), function () use ($from) {
            $response = Http::timeout(10)->get(self::EXCHANGE_API . $from);
            return $response->failed() ? null : $response->json();
        });

        if (! $data || ! isset($data['rates'][$to])) {
            return response()->json([
                'success' => false,
                'message' => "Could not retrieve rate for {$from} → {$to}. Check currency codes.",
            ], 422);
        }

        $rate   = (float) $data['rates'][$to];
        $result = round($amount * $rate, 2);

        return response()->json([
            'success'        => true,
            'message'        => 'Currency converted successfully.',
            'from'           => $from,
            'to'             => $to,
            'amount'         => $amount,
            'rate'           => $rate,
            'converted'      => $result,
            'last_updated'   => $data['time_last_update_utc'] ?? now()->toISOString(),
        ], 200);
    }
}
