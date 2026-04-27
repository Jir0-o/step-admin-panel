<?php

namespace App\Http\Controllers;

use App\Models\Store;
use App\Models\StoreRoute;
use App\Services\StoreTokenService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class StoreSummaryProxyController extends Controller
{
    public function __construct(private readonly StoreTokenService $tokenService)
    {
    }

    public function fetchSummary(Request $request, Store $store)
    {
        $tables = $request->query('tables') ?? $request->input('tables');

        if (! $tables) {
            return response()->json(['ok' => false, 'message' => 'Missing "tables" parameter'], 400);
        }

        if (! Auth::check()) {
            return response()->json(['ok' => false, 'message' => 'Unauthorized'], 401);
        }

        $params = ['tables' => $tables];
        foreach (['date_from', 'date_to', 'connection'] as $key) {
            if ($request->filled($key)) {
                $params[$key] = $request->input($key);
            }
        }

        try {
            $endpoint = $this->tokenService->buildStoreUrl($store, '/api/manager/data/summary');
            $userId = (int) Auth::id();

            $response = $this->tokenService->sendAuthorized(
                $store,
                $userId,
                'POST',
                $endpoint,
                [
                    'timeout' => 20,
                    'json' => $params,
                    'headers' => [
                        'Content-Type' => 'application/json',
                    ],
                ]
            );

            if (! $response->successful() && in_array($response->status(), [404, 405, 415, 422], true)) {
                $response = $this->tokenService->sendAuthorized(
                    $store,
                    $userId,
                    'GET',
                    $endpoint,
                    [
                        'timeout' => 20,
                        'query' => $params,
                    ]
                );
            }

            if (! $response->successful()) {
                return response()->json([
                    'ok' => false,
                    'message' => 'Remote server returned status '.$response->status(),
                    'store_id' => $store->id,
                ], 502);
            }

            $data = $response->json();

            if (isset($data['ok']) && $data['ok'] === false) {
                return response()->json([
                    'ok' => false,
                    'message' => $data['message'] ?? 'Remote server returned an error',
                    'store_id' => $store->id,
                ], 502);
            }

            return response()->json([
                'ok' => true,
                'store_id' => $store->id,
                'store_name' => $store->name,
                'data' => $data,
            ]);
        } catch (\Throwable $e) {
            Log::error('Store summary proxy failed.', [
                'store_id' => $store->id,
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'ok' => false,
                'message' => 'Failed to fetch data from remote server',
                'store_id' => $store->id,
            ], 502);
        }
    }

    public function finalStockDataProxy(Request $request, Store $store)
    {
        $route = StoreRoute::query()
            ->where('store_id', $store->id)
            ->where('endpoint', 'final-stock-data')
            ->where('is_active', true)
            ->first();

        if (! $route || ! $route->base_url) {
            return response()->json(['ok' => false, 'message' => 'Store route not configured'], 404);
        }

        try {
            $posUrl = rtrim($route->base_url, '/') . '/api/manager/data/final-stock-data';
            $response = $this->tokenService->sendAuthorized(
                $store,
                (int) Auth::id(),
                'GET',
                $posUrl,
                [
                    'timeout' => 60,
                    'query' => array_filter([
                        'page' => $request->query('page'),
                        'per_page' => $request->query('per_page'),
                        'store_id' => $store->id,
                    ]),
                ]
            );

            return response()->json($response->json(), $response->status());
        } catch (\Throwable $e) {
            Log::error('Final stock data proxy failed.', [
                'store_id' => $store->id,
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
            ]);

            return response()->json(['ok' => false, 'message' => 'Proxy error'], 500);
        }
    }
}
