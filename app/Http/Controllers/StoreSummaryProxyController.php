<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;
use GuzzleHttp\Client;
use App\Models\Store;
use App\Models\StoreRoute;
use App\Models\StoreToken;
use Illuminate\Support\Facades\Http;

class StoreSummaryProxyController extends Controller
{
    public function fetchSummary(Request $request, Store $store)
    {
        // Validate request
        $tables = $request->query('tables') ?? $request->input('tables');
        if (!$tables) {
            return response()->json(['ok' => false, 'message' => 'Missing "tables" parameter'], 400);
        }

        if (!Auth::check()) {
            return response()->json(['ok' => false, 'message' => 'Unauthorized'], 401);
        }

        // Get token for current user
        $tokenRow = StoreToken::where('store_id', $store->id)
            ->where('user_id', Auth::id())
            ->latest('created_at')
            ->first();

        if (!$tokenRow || empty($tokenRow->token)) {
            return response()->json(['ok' => false, 'message' => 'No access token found'], 403);
        }

        // Decrypt token
        try {
            $token = Crypt::decryptString($tokenRow->token);
        } catch (\Throwable $e) {
            Log::error("Token decrypt failed for store {$store->id}: " . $e->getMessage());
            return response()->json(['ok' => false, 'message' => 'Invalid token'], 500);
        }

        // Prepare endpoint URL
        $baseUrl = rtrim($store->base_url, '/');
        $summaryEndpoint = str_contains($baseUrl, '/api/manager/data/summary') 
            ? $baseUrl 
            : $baseUrl . '/api/manager/data/summary';

        // Prepare request parameters
        $params = ['tables' => $tables];
        foreach (['date_from', 'date_to', 'connection'] as $key) {
            if ($request->has($key)) {
                $params[$key] = $request->query($key);
            }
        }

        $client = new Client([
            'timeout' => 20,
            'http_errors' => false,
            'verify' => false,
        ]);

        // Try POST request first
        try {
            $response = $client->post($summaryEndpoint, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $token,
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                ],
                'json' => $params,
            ]);

            return $this->handleResponse($response, $store);
        } catch (\Throwable $e) {
            Log::warning("POST request failed: " . $e->getMessage());
        }

        // Fallback to GET request
        try {
            $url = $summaryEndpoint . '?' . http_build_query($params);
            
            $response = $client->get($url, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $token,
                    'Accept' => 'application/json',
                ],
            ]);

            return $this->handleResponse($response, $store);
        } catch (\Throwable $e) {
            Log::warning("GET request failed: " . $e->getMessage());
        }

        // All attempts failed
        return response()->json([
            'ok' => false,
            'message' => 'Failed to fetch data from remote server',
            'store_id' => $store->id,
        ], 502);
    }

    /**
     * Handle API response
     */
    private function handleResponse($response, Store $store)
    {
        $statusCode = $response->getStatusCode();
        $body = (string) $response->getBody();

        if ($statusCode >= 200 && $statusCode < 300) {
            $data = json_decode($body, true);

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
        }

        // Log failed response
        Log::error("Remote API returned status {$statusCode}", [
            'store_id' => $store->id,
            'response_body' => $body,
        ]);

        return response()->json([
            'ok' => false,
            'message' => "Remote server returned status {$statusCode}",
            'store_id' => $store->id,
        ], 502);
    }

    public function finalStockDataProxy(Request $request, \App\Models\Store $store)
    {
        $route = StoreRoute::where('store_id', $store->id)
            ->where('endpoint', 'final-stock-data') 
            ->where('is_active', true)
            ->first();


        if (! $route || ! $route->base_url) {
            return response()->json(['ok' => false, 'message' => 'Store route not configured'], 404);
        }

        // Build URL; POS endpoint path expected: /api/manager/data/final-stock-data
        $posUrl = rtrim($route->base_url, '/') . '/api/manager/data/final-stock-data';

        // forward page/per_page/store_id if present
        $query = [
            'page' => $request->query('page'),
            'per_page' => $request->query('per_page'),
            'store_id' => $store->id,
        ];

        // get latest token for store (mother app has StoreToken model)
        $tokenRow = StoreToken::where('store_id', $store->id)->latest('created_at')->first();
        if (! $tokenRow || empty($tokenRow->token)) {
            return response()->json(['ok' => false, 'message' => 'No API token'], 401);
        }

        try {
            $token = Crypt::decryptString($tokenRow->token);

            $resp = Http::withToken($token)->acceptJson()->get($posUrl, array_filter($query));

            return response()->json($resp->json(), $resp->status());

        } catch (\Throwable $e) {
            \Log::error('finalStockDataProxy error: '.$e->getMessage());
            return response()->json(['ok' => false, 'message' => 'Proxy error'], 500);
        }
    }
}