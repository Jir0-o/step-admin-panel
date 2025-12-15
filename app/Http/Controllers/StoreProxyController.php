<?php

namespace App\Http\Controllers;

use App\Models\Store;
use App\Models\StoreToken;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;

class StoreProxyController extends Controller
{
    public function fetchData(Request $request, Store $store)
    {
        // Authentication check
        if (!Auth::check()) {
            return response()->json(['ok' => false, 'message' => 'Unauthorized'], 401);
        }

        // Validate required parameters
        $request->validate([
            'tables' => 'required|string',
        ]);

        $user = Auth::user();

        // Get user's token for this store
        $tokenRow = StoreToken::where('store_id', $store->id)
            ->where('user_id', $user->id)
            ->latest()
            ->first();

        if (!$tokenRow || empty($tokenRow->token)) {
            return response()->json([
                'ok' => false,
                'message' => 'No access token available for this store',
            ], 403);
        }

        // Decrypt the token
        try {
            $token = Crypt::decryptString($tokenRow->token);
        } catch (\Throwable $e) {
            Log::error('Token decryption failed', [
                'store_id' => $store->id,
                'user_id' => $user->id,
            ]);

            return response()->json([
                'ok' => false,
                'message' => 'Invalid token configuration',
            ], 500);
        }

        // Build remote API endpoint
        $baseUrl = rtrim($store->base_url, '/');
        $endpoint = $baseUrl . '/api/manager/data/multi';

        // Prepare request parameters
        $params = $request->only([
            'tables',
            'date_from',
            'date_to',
            'connection',
            'limit',
            'page',
        ]);

        // Make HTTP request
        try {
            $client = new Client([
                'timeout' => 25,
                'http_errors' => false,
                'verify' => false,
            ]);

            $response = $client->post($endpoint, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $token,
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                ],
                'json' => $params,
            ]);

            $statusCode = $response->getStatusCode();
            $body = (string) $response->getBody();

            if ($statusCode >= 200 && $statusCode < 300) {
                $data = json_decode($body, true);

                // Check if remote API returned an error
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
            }

            // Handle non-2xx responses
            throw new \Exception("Remote API returned status {$statusCode}");

        } catch (\Throwable $e) {
            Log::error('Failed to fetch store data', [
                'store_id' => $store->id,
                'user_id' => $user->id,
                'endpoint' => $endpoint,
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