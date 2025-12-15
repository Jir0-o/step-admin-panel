<?php

namespace App\Services;

use App\Models\Store;
use App\Models\StoreToken;
use Carbon\Carbon;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;

class StoreTokenSyncService
{
    private Client $client;
    private int $refreshBeforeSeconds;

    public function __construct()
    {
        $this->client = new Client([
            'timeout' => 10,
            'connect_timeout' => 5,
            'http_errors' => false,
        ]);

        $this->refreshBeforeSeconds = config('services.store_token.refresh_before_seconds', 300);
    }

    /**
     * Sync tokens for a specific user across all stores
     */
    public function syncForUser(int $userId): array
    {
        $results = [];
        $stores = $this->getStoresWithLoginApi();

        foreach ($stores as $store) {
            if (!$this->hasValidCredentials($store)) {
                $results[$store->id] = 'missing_credentials';
                continue;
            }

            if ($this->shouldSkipRefresh($store, $userId)) {
                $results[$store->id] = 'token_valid';
                continue;
            }

            $result = $this->refreshToken($store, $userId);
            $results[$store->id] = $result;
        }

        return $results;
    }

    /**
     * Get stores that have login API configured
     */
    private function getStoresWithLoginApi()
    {
        return Store::whereNotNull('login_api_url')
            ->where('login_api_url', '!=', '')
            ->get();
    }

    /**
     * Check if store has required credentials
     */
    private function hasValidCredentials(Store $store): bool
    {
        return !empty($store->user_email) && !empty($store->user_password);
    }

    /**
     * Check if token refresh should be skipped
     */
    private function shouldSkipRefresh(Store $store, int $userId): bool
    {
        $token = StoreToken::where('store_id', $store->id)
            ->where('user_id', $userId)
            ->latest()
            ->first();

        if (!$token || !$token->expires_at) {
            return false;
        }

        $secondsLeft = Carbon::now()->diffInSeconds($token->expires_at, false);
        return $secondsLeft > $this->refreshBeforeSeconds;
    }

    /**
     * Refresh token for a specific store and user
     */
    private function refreshToken(Store $store, int $userId): string
    {
        try {
            $password = Crypt::decryptString($store->user_password);

            $response = $this->client->post($store->login_api_url, [
                'json' => [
                    'login' => $store->user_email,
                    'password' => $password,
                ],
                'headers' => [
                    'Accept' => 'application/json',
                ],
            ]);

            $data = json_decode((string) $response->getBody(), true);

            if (empty($data['token'])) {
                throw new \Exception($data['message'] ?? 'No token in response');
            }

            $this->updateOrCreateToken($store, $userId, $data);

            return 'refreshed';

        } catch (\Throwable $e) {
            Log::error('Token refresh failed', [
                'store_id' => $store->id,
                'user_id' => $userId,
                'error' => $e->getMessage(),
            ]);

            return 'failed';
        }
    }

    /**
     * Update or create token record
     */
    private function updateOrCreateToken(Store $store, int $userId, array $data): void
    {
        StoreToken::updateOrCreate(
            [
                'store_id' => $store->id,
                'user_id' => $userId,
            ],
            [
                'token' => Crypt::encryptString($data['token']),
                'expires_at' => isset($data['expires_in']) 
                    ? Carbon::now()->addSeconds((int) $data['expires_in'])
                    : null,
                'updated_at' => Carbon::now(),
            ]
        );
    }
}