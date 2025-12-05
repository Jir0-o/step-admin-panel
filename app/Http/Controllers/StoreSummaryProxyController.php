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
        // Validate required fields
        // -------------------------------
        $tables = $request->query('tables') ?? $request->input('tables');
        if (empty($tables)) {
            return response()->json(['ok' => false, 'message' => 'Missing "tables" parameter'], 400);
        }

        if (!Auth::id()) {
            return response()->json(['ok' => false, 'message' => 'Unauthenticated'], 401);
        }

        // -------------------------------
        // Fetch token (safe fallback)
        // -------------------------------
        try {
            $tokenRow = StoreToken::where('store_id', $store->id)
                ->where('user_id', Auth::id())
                ->latest('created_at')
                ->first();
        } catch (\Throwable $e) {
            Log::warning("StoreSummaryProxy: token fallback: ".$e->getMessage());
            $tokenRow = DB::table('store_tokens')
                ->where('store_id', $store->id)
                ->where('user_id', Auth::id())
                ->orderByDesc('created_at')
                ->first();
        }

        $encrypted = $tokenRow->token ?? ($tokenRow['token'] ?? null);
        if (!$encrypted) {
            return response()->json(['ok' => false, 'message' => 'No token found'], 403);
        }

        try {
            $token = Crypt::decryptString($encrypted);
        } catch (\Throwable $e) {
            Log::error("StoreSummaryProxy: decrypt failed for store {$store->id}: ".$e->getMessage());
            return response()->json(['ok' => false, 'message' => 'Failed to decrypt token'], 500);
        }

        // -------------------------------
        // Build canonical origin
        // -------------------------------
        $baseUrl  = rtrim($store->base_url ?? '', '/');
        $loginUrl = rtrim($store->login_api_url ?? '', '/');

        $origin = null;

        // 1) Try base_url origin
        if ($baseUrl) {
            $p = parse_url($baseUrl);
            if (!empty($p['host'])) {
                $origin = ($p['scheme'] ?? 'http') . '://' . $p['host'] . (isset($p['port']) ? ':' . $p['port'] : '');
            }
        }

        // 2) fallback to login_url origin
        if (!$origin && $loginUrl) {
            $p = parse_url($loginUrl);
            if (!empty($p['host'])) {
                $origin = ($p['scheme'] ?? 'http') . '://' . $p['host'] . (isset($p['port']) ? ':' . $p['port'] : '');
            }
        }

        if (!$origin) {
            return response()->json(['ok' => false, 'message' => 'Invalid store base URL'], 500);
        }

        // -------------------------------
        // Detect /public prefix
        // -------------------------------
        $publicPrefix = '';

        foreach ([$baseUrl, $loginUrl] as $u) {
            if ($u && str_contains(parse_url($u, PHP_URL_PATH) ?: '', '/public')) {
                $publicPrefix = '/public';
                break;
            }
        }

        // -------------------------------
        // FINAL SUMMARY ENDPOINT
        // -------------------------------
        $summaryEndpoint = $origin . $publicPrefix . '/api/manager/data/summary';

        Log::info("StoreSummaryProxy: Using summary endpoint => {$summaryEndpoint}");

        // -------------------------------
        // Forward params
        // -------------------------------
        $forward = ['tables' => $tables];
        foreach (['date_from','date_to','connection'] as $opt) {
            if ($request->has($opt)) {
                $forward[$opt] = $request->query($opt);
            }
        }

        $client = new Client(['timeout' => 20, 'http_errors' => false, 'verify' => false]);
        $last = null;

        // -------------------------------
        // 1) Try POST (preferred)
        // -------------------------------
        try {
            $resp = $client->post($summaryEndpoint, [
                'headers' => [
                    'Authorization' => 'Bearer '.$token,
                    'Accept' => 'application/json'
                ],
                'json' => $forward
            ]);

            $status = $resp->getStatusCode();
            $body = (string)$resp->getBody();
            $last = ['method'=>'POST','url'=>$summaryEndpoint,'status'=>$status,'body'=>substr($body,0,5000)];

            Log::info("StoreSummaryProxy: POST {$summaryEndpoint} -> {$status}");

            if ($status >= 200 && $status < 300) {
                return response()->json([
                    'ok'=>true,
                    'store_id'=>$store->id,
                    'store_name'=>$store->name,
                    'data'=>json_decode($body, true) ?: $body
                ]);
            }
        } catch (\Throwable $e) {
            Log::warning("StoreSummaryProxy POST error: ".$e->getMessage());
        }

        // -------------------------------
        // 2) Try GET fallback
        // -------------------------------
        try {
            $urlGet = $summaryEndpoint.'?'.http_build_query($forward);

            $resp = $client->get($urlGet, [
                'headers' => [
                    'Authorization' => 'Bearer '.$token,
                    'Accept' => 'application/json'
                ]
            ]);

            $status = $resp->getStatusCode();
            $body = (string)$resp->getBody();
            $last = ['method'=>'GET','url'=>$urlGet,'status'=>$status,'body'=>substr($body,0,5000)];

            Log::info("StoreSummaryProxy: GET {$urlGet} -> {$status}");

            if ($status >= 200 && $status < 300) {
                return response()->json([
                    'ok'=>true,
                    'store_id'=>$store->id,
                    'store_name'=>$store->name,
                    'data'=>json_decode($body, true) ?: $body
                ]);
            }
        } catch (\Throwable $e) {
            Log::warning("StoreSummaryProxy GET error: ".$e->getMessage());
        }

        // -------------------------------
        // Final fail
        // -------------------------------
        return response()->json([
            'ok'=>false,
            'message'=>'Remote summary endpoint failed',
            'store_id'=>$store->id,
            'store_name'=>$store->name,
            'endpoint'=>$summaryEndpoint,
            'last_attempt'=>$last
        ], 502);
    }
}
