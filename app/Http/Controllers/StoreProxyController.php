<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;
use GuzzleHttp\Client;
use App\Models\Store;
use App\Models\StoreToken;

class StoreProxyController extends Controller
{
    /**
     * Proxy a data request to a remote store's API using stored token.
     * Example:
     * /stores/{store}/fetch-data?tables=products,suppliers
     */
    public function fetchData(Request $request, Store $store)
    {
        // -------------------------------
        // Validate
        // -------------------------------
        $tables = $request->query('tables');
        if (!$tables) {
            return response()->json(['ok' => false, 'message' => 'Missing "tables" parameter'], 400);
        }

        if (!Auth::check()) {
            return response()->json(['ok' => false, 'message' => 'Unauthenticated'], 401);
        }

        // -------------------------------
        // Fetch latest token (ANY user)
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
            Log::error("StoreProxy token decrypt failed for store {$store->id}: ".$e->getMessage());
            return response()->json(['ok' => false, 'message' => 'Invalid token'], 500);
        }

        // -------------------------------
        // USE STORED DATA URL DIRECTLY
        // -------------------------------
        $dataEndpoint = rtrim($store->base_url, '/');

        if (!str_contains($dataEndpoint, '/api/manager/data/multi')) {
            return response()->json([
                'ok' => false,
                'message' => 'Invalid data endpoint URL in database',
                'base_url' => $dataEndpoint
            ], 500);
        }

        // -------------------------------
        // Params
        // -------------------------------
        $params = ['tables' => $tables];

        $client = new Client([
            'timeout' => 20,
            'http_errors' => false,
            'verify' => false,
        ]);

        // -------------------------------
        // POST (primary)
        // -------------------------------
        try {
            $resp = $client->post($dataEndpoint, [
                'headers' => [
                    'Authorization' => 'Bearer '.$token,
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
            Log::warning("StoreProxy POST failed: ".$e->getMessage());
        }

        // -------------------------------
        // GET fallback
        // -------------------------------
        try {
            $url = $dataEndpoint.'?'.http_build_query($params);

            $resp = $client->get($url, [
                'headers' => [
                    'Authorization' => 'Bearer '.$token,
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
            Log::warning("StoreProxy GET failed: ".$e->getMessage());
        }

        // -------------------------------
        // Fail
        // -------------------------------
        return response()->json([
            'ok' => false,
            'message' => 'Remote data endpoint failed',
            'endpoint' => $dataEndpoint,
        ], 502);
    }
}
