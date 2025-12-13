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

class StoreProxyController extends Controller
{
    /**
     * Proxy a data request to a remote store's API using stored token.
     * Example: /stores/4/fetch-data?tables=products,suppliers
     */
    public function fetchData(Request $request, Store $store)
    {


        $tables = $request->query('tables');
        if (empty($tables)) {
            return response()->json(['ok' => false, 'message' => 'Missing "tables" parameter'], 400);
        }

        $userId = Auth::id();
        if (!$userId) {
            return response()->json(['ok' => false, 'message' => 'Unauthenticated'], 401);
        }

        // fetch latest token for this user + store
        $tokenRow = null;
        try {
            $tokenRow = StoreToken::where('store_id', $store->id)
                        ->latest('created_at')
                        ->first();
        } catch (\Throwable $e) {
            Log::warning("StoreProxy: token model query failed, fallback DB: ".$e->getMessage());
            $tokenRow = DB::table('store_tokens')
                        ->where('store_id', $store->id)
                        ->orderByDesc('created_at')
                        ->first();
        }

        if (!$tokenRow || empty($tokenRow->token)) {
            return response()->json(['ok' => false, 'message' => 'No token found for this store/user'], 403);
        }

        // decrypt token
        try {
            $encrypted = $tokenRow->token;
            $token = Crypt::decryptString($encrypted);
        } catch (\Throwable $e) {
            Log::error("StoreProxy: decrypt token failed for store {$store->id}: ".$e->getMessage());
            return response()->json(['ok' => false, 'message' => 'Failed to decrypt token'], 500);
        }

        // build remote URL: prefer replacing known login path if present, otherwise use base_url
        $loginUrl = rtrim($store->login_api_url ?? '', '/');
        $baseUrl = rtrim($store->base_url ?? '', '/');

        if (!empty($loginUrl) && strpos($loginUrl, '/api/backoffice/login') !== false) {
            $remote = str_replace('/api/backoffice/login', '/api/manager/data/multi', $loginUrl);
        } else {
            // fallback to base_url + expected path
            $remote = ($baseUrl ?: $loginUrl) . '/api/manager/data/multi';
        }

        // append query param tables (use GET with query)
        $sep = (strpos($remote, '?') === false) ? '?' : '&';
        $remoteWithQuery = $remote . $sep . 'tables=' . urlencode($tables);

        $client = new Client(['timeout' => 10, 'http_errors' => false]);

        try {
            $resp = $client->request('POST', $remoteWithQuery, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $token,
                    'Accept' => 'application/json',
                    'User-Agent' => 'MotherApp-Proxy/1.0'
                ],
                // some stores expect JSON body; many accept empty POST with query params — we send none
                'timeout' => 10,
                'http_errors' => false
            ]);

            $status = $resp->getStatusCode();
            $body = (string) $resp->getBody();
            $json = null;
            if ($body) {
                $decoded = json_decode($body, true);
                if (json_last_error() === JSON_ERROR_NONE) $json = $decoded;
            }

            // if remote returned non-2xx and JSON contains message, forward it
            if ($status >= 400) {
                Log::warning("StoreProxy: remote returned {$status} for store {$store->id}", ['url'=>$remoteWithQuery, 'body'=>substr($body,0,2000)]);
                return response()->json([
                    'ok' => false,
                    'store_id' => $store->id,
                    'store_name' => $store->name,
                    'status' => $status,
                    'remote' => $remoteWithQuery,
                    'message' => $json['message'] ?? 'Remote error',
                    'remote_raw' => $json ?? substr($body,0,2000)
                ], 502);
            }

            // success — return wrapped response to client
            return response()->json([
                'ok' => true,
                'store_id' => $store->id,
                'store_name' => $store->name,
                'status' => $status,
                'data' => $json ?? $body
            ], 200);

        } catch (\Throwable $e) {
            Log::error("StoreProxy: request failed for store {$store->id}: ".$e->getMessage());
            return response()->json(['ok' => false, 'message' => 'Proxy request failed', 'error' => $e->getMessage()], 500);
        }
    }
}
