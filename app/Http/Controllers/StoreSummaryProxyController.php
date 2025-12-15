<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use GuzzleHttp\Client;
use App\Models\Store;
use App\Models\StoreToken;

class StoreSummaryProxyController extends Controller
{
    /**
     * Proxy a request to remote store summary endpoint.
     * Example GET: /stores/{store}/fetch-summary?tables=cart_informtion,expenses
     * Optional params forwarded: tables, date_from, date_to, connection
     */
    public function fetchSummary(Request $request, Store $store)
    {
        // -------------------------------
        // Validate
        // -------------------------------
        $tables = $request->query('tables') ?? $request->input('tables');
        if (!$tables) {
            return response()->json(['ok' => false, 'message' => 'Missing "tables" parameter'], 400);
        }

        if (!Auth::check()) {
            return response()->json(['ok' => false, 'message' => 'Unauthenticated'], 401);
        }

        // -------------------------------
        // Fetch token (latest, any user)
        // -------------------------------
        $tokenRow = StoreToken::where('store_id', $store->id)
            ->latest('created_at')
            ->first();

        if (!$tokenRow || empty($tokenRow->token)) {
            return response()->json(['ok' => false, 'message' => 'No token found'], 403);
        }

        try {
            $token = Crypt::decryptString($tokenRow->token);
        } catch (\Throwable $e) {
            Log::error("Token decrypt failed for store {$store->id}: ".$e->getMessage());
            return response()->json(['ok' => false, 'message' => 'Invalid token'], 500);
        }

        // -------------------------------
        // USE STORED SUMMARY URL DIRECTLY
        // -------------------------------
        $summaryEndpoint = rtrim($store->base_url, '/');

        if (!str_contains($summaryEndpoint, '/api/manager/data/summary')) {
            return response()->json([
                'ok' => false,
                'message' => 'Invalid summary URL in database',
                'base_url' => $summaryEndpoint
            ], 500);
        }

        // -------------------------------
        // Forward params
        // -------------------------------
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

        // -------------------------------
        // POST (primary)
        // -------------------------------
        try {
            $resp = $client->post($summaryEndpoint, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $token,
                    'Accept'        => 'application/json',
                ],
                'json' => $params,
            ]);

            if ($resp->getStatusCode() >= 200 && $resp->getStatusCode() < 300) {
                return response()->json([
                    'ok' => true,
                    'store_id' => $store->id,
                    'store_name' => $store->name,
                    'data' => json_decode((string)$resp->getBody(), true),
                ]);
            }
        } catch (\Throwable $e) {
            Log::warning("POST summary failed: ".$e->getMessage());
        }

        // -------------------------------
        // GET fallback
        // -------------------------------
        try {
            $url = $summaryEndpoint . '?' . http_build_query(data: $params);

            $resp = $client->get($url, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $token,
                    'Accept'        => 'application/json',
                ],
            ]);

            if ($resp->getStatusCode() >= 200 && $resp->getStatusCode() < 300) {
                return response()->json([
                    'ok' => true,
                    'store_id' => $store->id,
                    'store_name' => $store->name,
                    'data' => json_decode((string)$resp->getBody(), true),
                ]);
            }
        } catch (\Throwable $e) {
            Log::warning("GET summary failed: ".$e->getMessage());
        }

        // -------------------------------
        // Fail
        // -------------------------------
        return response()->json([
            'ok' => false,
            'message' => 'Remote summary endpoint failed',
            'endpoint' => $summaryEndpoint,
        ], 502);
    }

}
