<?php

namespace App\Services;

use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;
use GuzzleHttp\Client;
use App\Models\Store;
use Carbon\Carbon;
use App\Models\StoreToken;

class StoreTokenSyncService
{
    public function syncForUser(int $userId): array
    {
        $results = [];

        $refreshBefore = config('services.store_token.refresh_before_seconds', 300);

        $client = new Client([
            'timeout' => 8,
            'connect_timeout' => 3,
            'http_errors' => false,
        ]);

        $stores = Store::whereNotNull('login_api_url')
            ->where('login_api_url', '!=', '')
            ->get();

        foreach ($stores as $store) {

            if (! $store->user_email || ! $store->user_password) {
                $results[$store->id] = 'missing_store_credentials';
                continue;
            }

            // 🔹 Check existing token
            $existingToken = StoreToken::where('store_id', $store->id)
                ->where('user_id', $userId)
                ->latest()
                ->first();

            if ($existingToken && $existingToken->expires_at) {
                $secondsLeft = Carbon::now()->diffInSeconds(
                    $existingToken->expires_at,
                    false
                );

                if ($secondsLeft > $refreshBefore) {
                    $results[$store->id] = 'token_valid_skipped';
                    continue;
                }
            }


            try {
                $password = Crypt::decryptString($store->user_password);

                $resp = $client->post($store->login_api_url, [
                    'json' => [
                        'login'    => $store->user_email,
                        'password' => $password,
                    ],
                    'headers' => [
                        'Accept' => 'application/json',
                        'User-Agent' => 'MotherApp/1.0',
                    ],
                ]);

                $body = json_decode((string) $resp->getBody(), true);

                if (empty($body['token'])) {
                    throw new \Exception($body['message'] ?? 'Token missing');
                }

                StoreToken::updateOrCreate(
                    [
                        'store_id' => $store->id,
                        'user_id'  => $userId,
                    ],
                    [
                        'token' => Crypt::encryptString($body['token']),
                        'expires_at' => isset($body['expires_in'])
                            ? now()->addSeconds((int)$body['expires_in'])
                            : null,
                    ]
                );

                $results[$store->id] = 'token_refreshed';

            } catch (\Throwable $e) {
                Log::error('Store token refresh failed', [
                    'store_id' => $store->id,
                    'error' => $e->getMessage()
                ]);
                $results[$store->id] = 'refresh_failed';
            }
        }

        return $results;
    }
}
