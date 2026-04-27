<?php

namespace App\Http\Controllers;

use App\Models\Store;
use App\Services\StoreTokenService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class StoreProxyController extends Controller
{
    public function __construct(private readonly StoreTokenService $tokenService)
    {
    }

    public function fetchData(Request $request, Store $store)
    {
        if (! Auth::check()) {
            return response()->json(['ok' => false, 'message' => 'Unauthorized'], 401);
        }

        $request->validate([
            'tables' => 'required|string',
        ]);

        $params = $request->only([
            'tables',
            'date_from',
            'date_to',
            'connection',
            'limit',
            'page',
        ]);

        try {
            $endpoint = $this->tokenService->buildStoreUrl($store, '/api/manager/data/multi');

            $response = $this->tokenService->sendAuthorized(
                $store,
                (int) Auth::id(),
                'POST',
                $endpoint,
                [
                    'timeout' => 25,
                    'json' => $params,
                    'headers' => [
                        'Content-Type' => 'application/json',
                    ],
                ]
            );

            if (! $response->successful()) {
                return response()->json([
                    'ok' => false,
                    'message' => 'Failed to retrieve data from store server',
                    'store_id' => $store->id,
                ], 502);
            }

            $data = $response->json();

            if (isset($data['ok']) && $data['ok'] === false) {
                return response()->json([
                    'ok' => false,
                    'message' => $data['message'] ?? 'Remote API error',
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
            Log::error('Failed to fetch store data.', [
                'store_id' => $store->id,
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'ok' => false,
                'message' => 'Failed to retrieve data from store server',
                'store_id' => $store->id,
            ], 502);
        }
    }
}
