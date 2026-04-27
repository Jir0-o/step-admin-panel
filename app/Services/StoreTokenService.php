<?php

namespace App\Services;

use App\Models\Store;
use App\Models\StoreToken;
use Carbon\Carbon;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use RuntimeException;

class StoreTokenService
{
    private int $refreshBeforeSeconds;

    public function __construct()
    {
        $this->refreshBeforeSeconds = (int) config('services.store_token.refresh_before_seconds', 300);
    }

    public function syncForUser(int $userId, ?string $fallbackEmail = null, ?string $fallbackPassword = null): array
    {
        $results = [];

        $stores = Store::query()
            ->whereNotNull('login_api_url')
            ->where('login_api_url', '!=', '')
            ->orderBy('id')
            ->get();

        foreach ($stores as $store) {
            $credentials = $this->resolveCredentials($store, $fallbackEmail, $fallbackPassword);

            if (! $credentials) {
                $results[$store->id] = 'missing_credentials';
                continue;
            }

            try {
                $this->getValidTokenForUser(
                    $store,
                    $userId,
                    $fallbackEmail,
                    $fallbackPassword,
                    false
                );

                $results[$store->id] = 'ready';
            } catch (\Throwable $e) {
                Log::warning('Store token sync failed', [
                    'store_id' => $store->id,
                    'user_id' => $userId,
                    'error' => $e->getMessage(),
                ]);

                $results[$store->id] = 'failed';
            }
        }

        return $results;
    }

    public function getValidTokenForUser(
        Store $store,
        int $userId,
        ?string $fallbackEmail = null,
        ?string $fallbackPassword = null,
        bool $forceRefresh = false
    ): string {
        if (! $forceRefresh) {
            $cachedToken = Cache::get($this->cacheKey($store->id, $userId));
            if (is_string($cachedToken) && $cachedToken !== '') {
                return $cachedToken;
            }
        }

        $tokenRow = StoreToken::query()
            ->where('store_id', $store->id)
            ->where('user_id', $userId)
            ->latest('created_at')
            ->first();

        $credentials = $this->resolveCredentials($store, $fallbackEmail, $fallbackPassword);

        if ($tokenRow) {
            try {
                $token = Crypt::decryptString($tokenRow->token);

                if ($forceRefresh && $credentials) {
                    return $this->refreshToken($store, $userId, $credentials['email'], $credentials['password']);
                }

                if (! $tokenRow->isNearExpiry($this->refreshBeforeSeconds)) {
                    $this->putTokenInCache($store->id, $userId, $token, $tokenRow->expires_at);
                    return $token;
                }

                if (! $credentials) {
                    Log::warning('Store token is near expiry but no refresh credentials are available; using existing token.', [
                        'store_id' => $store->id,
                        'user_id' => $userId,
                    ]);
                    $this->putTokenInCache($store->id, $userId, $token, $tokenRow->expires_at);
                    return $token;
                }

                try {
                    return $this->refreshToken($store, $userId, $credentials['email'], $credentials['password']);
                } catch (\Throwable $refreshError) {
                    Log::warning('Token refresh failed; falling back to existing token.', [
                        'store_id' => $store->id,
                        'user_id' => $userId,
                        'error' => $refreshError->getMessage(),
                    ]);
                    $this->putTokenInCache($store->id, $userId, $token, $tokenRow->expires_at);
                    return $token;
                }
            } catch (\Throwable $e) {
                Log::warning('Stored token decrypt failed.', [
                    'store_id' => $store->id,
                    'user_id' => $userId,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        if (! $credentials) {
            throw new RuntimeException('Store login credentials are missing and no stored token is available.');
        }

        return $this->refreshToken($store, $userId, $credentials['email'], $credentials['password']);
    }

    public function refreshToken(Store $store, int $userId, string $email, string $password): string
    {
        $lock = Cache::lock("store-token-refresh:{$store->id}:{$userId}", 10);

        try {
            return $lock->block(5, function () use ($store, $userId, $email, $password) {
                $response = Http::acceptJson()
                    ->timeout(15)
                    ->asJson()
                    ->post($store->login_api_url, [
                        'login' => $email,
                        'password' => $password,
                    ]);

                if (! $response->successful()) {
                    throw new RuntimeException('Store login failed with status '.$response->status().'.');
                }

                $data = $response->json();
                $token = Arr::get($data, 'token');

                if (! is_string($token) || $token === '') {
                    throw new RuntimeException(Arr::get($data, 'message', 'Store login response did not include a token.'));
                }

                $expiresAt = null;
                if (Arr::has($data, 'expires_in')) {
                    $expiresAt = now()->addSeconds((int) Arr::get($data, 'expires_in'));
                }

                StoreToken::query()->updateOrCreate(
                    [
                        'store_id' => $store->id,
                        'user_id' => $userId,
                    ],
                    [
                        'token' => Crypt::encryptString($token),
                        'expires_at' => $expiresAt,
                        'meta' => [
                            'synced_via' => 'store_token_service',
                            'login_api_url' => $store->login_api_url,
                        ],
                    ]
                );

                $this->putTokenInCache($store->id, $userId, $token, $expiresAt);

                return $token;
            });
        } finally {
            optional($lock)->release();
        }
    }

    public function sendAuthorized(
        Store $store,
        int $userId,
        string $method,
        string $url,
        array $options = [],
        ?string $fallbackEmail = null,
        ?string $fallbackPassword = null,
        bool $retryOnUnauthorized = true
    ): Response {
        $token = $this->getValidTokenForUser($store, $userId, $fallbackEmail, $fallbackPassword);
        $response = $this->sendHttpRequest($token, $method, $url, $options);

        if ($retryOnUnauthorized && in_array($response->status(), [401, 403], true)) {
            $credentials = $this->resolveCredentials($store, $fallbackEmail, $fallbackPassword);
            if (! $credentials) {
                Log::warning('Remote request was unauthorized but refresh credentials are unavailable.', [
                    'store_id' => $store->id,
                    'user_id' => $userId,
                    'status' => $response->status(),
                    'url' => $url,
                ]);
                return $response;
            }

            $token = $this->refreshToken($store, $userId, $credentials['email'], $credentials['password']);
            $response = $this->sendHttpRequest($token, $method, $url, $options);
        }

        return $response;
    }

    public function buildStoreUrl(Store $store, string $path): string
    {
        if (preg_match('/^https?:\/\//i', $path) === 1) {
            return $path;
        }

        $baseUrl = rtrim((string) $store->base_url, '/');
        $normalizedPath = '/' . ltrim($path, '/');

        if ($baseUrl === '') {
            return $normalizedPath;
        }

        $basePath = parse_url($baseUrl, PHP_URL_PATH) ?: '';
        if ($basePath !== '' && str_ends_with(rtrim($basePath, '/'), rtrim($normalizedPath, '/'))) {
            return $baseUrl;
        }

        return $baseUrl . $normalizedPath;
    }

    private function sendHttpRequest(string $token, string $method, string $url, array $options = []): Response
    {
        $pending = Http::acceptJson()
            ->timeout((int) ($options['timeout'] ?? 30))
            ->withToken($token);

        if (! empty($options['headers']) && is_array($options['headers'])) {
            $pending = $pending->withHeaders($options['headers']);
        }

        $sendOptions = [];

        if (array_key_exists('query', $options)) {
            $sendOptions['query'] = $options['query'];
        }

        if (array_key_exists('json', $options)) {
            $sendOptions['json'] = $options['json'];
        }

        if (array_key_exists('body', $options)) {
            $sendOptions['body'] = $options['body'];
        }

        return $pending->send(strtoupper($method), $url, $sendOptions);
    }

    private function resolveCredentials(Store $store, ?string $fallbackEmail, ?string $fallbackPassword): ?array
    {
        $email = $store->user_email ?: $fallbackEmail;
        $password = $store->getDecryptedPassword() ?: $fallbackPassword;

        if (! $email || ! $password) {
            return null;
        }

        return [
            'email' => $email,
            'password' => $password,
        ];
    }

    private function putTokenInCache(int $storeId, int $userId, string $token, $expiresAt = null): void
    {
        $ttlSeconds = 1800;

        if ($expiresAt instanceof Carbon) {
            $ttlSeconds = max(60, now()->diffInSeconds($expiresAt, false) - 60);
        }

        Cache::put($this->cacheKey($storeId, $userId), $token, now()->addSeconds($ttlSeconds));
    }

    private function cacheKey(int $storeId, int $userId): string
    {
        return "store-token:{$storeId}:user:{$userId}";
    }
}
