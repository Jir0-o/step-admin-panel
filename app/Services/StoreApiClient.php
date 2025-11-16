<?php

namespace App\Services;

use App\Models\Store;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Psr\SimpleCache\InvalidArgumentException;

class StoreApiClient
{
    protected int $timeout = 30;

    /**
     * Try to login and return an auth token or cookie jar for subsequent requests.
     * Returns array: ['ok'=>bool, 'type'=>'token'|'cookie', 'token'=>string, 'cookies'=>array, 'base_url'=>string]
     */
    public function login(Store $store): array
    {
        $base = rtrim($store->base_url, '/');

        $email = $store->user_email;
        $password = $store->getDecryptedPassword();

        // 1) Try token endpoint: /sanctum/token or /api/token etc. Adjust per remote app.
        $tokenCandidates = [
            '/sanctum/token',
            '/api/token',
            '/api/login',           // common in custom apps
            '/login'                // some systems return session cookie here
        ];

        foreach ($tokenCandidates as $path) {
            $url = $base . $path;
            try {
                $resp = Http::timeout($this->timeout)->asForm()->post($url, [
                    'email' => $email,
                    'password' => $password,
                    // some systems require name for token creation, others return token directly
                    // 'device_name' => 'all-in-one-sync'
                ]);

                if ($resp->successful()) {
                    $json = $resp->json();

                    // if remote returned token in JSON
                    if (is_array($json) && (isset($json['token']) || isset($json['access_token']) || isset($json['plainTextToken']))) {
                        $token = $json['token'] ?? $json['access_token'] ?? $json['plainTextToken'];
                        return ['ok' => true, 'type' => 'token', 'token' => $token, 'base_url' => $base];
                    }

                    // some returns ok: true + data object containing token
                    if (isset($json['ok']) && isset($json['data']['token'])) {
                        return ['ok' => true, 'type' => 'token', 'token' => $json['data']['token'], 'base_url' => $base];
                    }

                    // fallback: remote may have set cookies (session cookie)
                    $cookies = $resp->cookies();
                    if (!empty($cookies)) {
                        return ['ok' => true, 'type' => 'cookie', 'cookies' => $cookies, 'base_url' => $base];
                    }
                }
            } catch (\Throwable $e) {
                // try next candidate
            }
        }

        // 2) fallback: do POST to /login and capture cookies manually using Guzzle
        try {
            $guzzle = new \GuzzleHttp\Client(['timeout' => $this->timeout, 'allow_redirects' => true]);
            $loginUrl = $base . '/login';
            $gRes = $guzzle->request('POST', $loginUrl, [
                'form_params' => [
                    'email' => $email,
                    'password' => $password,
                ],
                'headers' => [
                    'Accept' => 'application/json'
                ]
            ]);

            // gather cookies
            $jar = \GuzzleHttp\Cookie\SetCookie::fromString((string) $gRes->getHeaderLine('Set-Cookie'));
            $cookies = $gRes->getHeader('Set-Cookie');

            return ['ok' => true, 'type' => 'cookie', 'cookies' => $cookies, 'base_url' => $base];
        } catch (\Throwable $e) {
            return ['ok' => false, 'message' => 'Login failed: ' . $e->getMessage()];
        }
    }

    /**
     * Fetch export JSON from remote store.
     * Accepts optional $tokenOrCookies from login() output.
     * Will attempt JSON endpoint patterns and return the first found result.
     */
    public function fetchExport(Store $store, array $auth = [], array $params = []): array
    {
        $base = rtrim($store->base_url, '/');

        $endpoints = [
            '/api/manager/export/json',
            '/api/manager/all',
            '/api/export/json',
            '/api/export/all',
            '/public/backoffice/api/orders',
            '/api/orders',
            '/orders/data'
        ];

        // If we have token -> use Bearer
        if (!empty($auth['type']) && $auth['type'] === 'token' && !empty($auth['token'])) {
            foreach ($endpoints as $ep) {
                try {
                    $url = $base . $ep;
                    $r = Http::timeout($this->timeout)
                        ->withToken($auth['token'])
                        ->get($url, $params);
                    if ($r->successful()) {
                        return ['ok' => true, 'endpoint' => $url, 'data' => $r->json()];
                    }
                } catch (\Throwable $e) {
                    continue;
                }
            }
            return ['ok' => false, 'message' => 'No reachable JSON endpoint with token'];
        }

        // If cookie-based -> replay login cookie with Guzzle (Http facade cookie support is limited)
        if (!empty($auth['type']) && $auth['type'] === 'cookie' && !empty($auth['cookies'])) {
            try {
                $client = new \GuzzleHttp\Client(['timeout' => $this->timeout, 'allow_redirects' => true]);
                foreach ($endpoints as $ep) {
                    $url = $base . $ep;
                    try {
                        $res = $client->request('GET', $url, [
                            'headers' => [
                                'Accept' => 'application/json',
                                'Cookie' => implode('; ', $auth['cookies'] ?? []),
                            ],
                            'query' => $params
                        ]);
                        $code = $res->getStatusCode();
                        if ($code >= 200 && $code < 300) {
                            $body = (string)$res->getBody();
                            $json = json_decode($body, true);
                            return ['ok' => true, 'endpoint' => $url, 'data' => $json];
                        }
                    } catch (\Throwable $e) {
                        continue;
                    }
                }
            } catch (\Throwable $e) {
                return ['ok' => false, 'message' => 'Cookie replay failed: '.$e->getMessage()];
            }
        }

        // Last fallback: try unauthenticated GETs (if remote allows read)
        foreach ($endpoints as $ep) {
            try {
                $r = Http::timeout($this->timeout)->get($base . $ep, $params);
                if ($r->successful()) {
                    return ['ok' => true, 'endpoint' => $base . $ep, 'data' => $r->json()];
                }
            } catch (\Throwable $e) {
                continue;
            }
        }

        return ['ok' => false, 'message' => 'No endpoint responded'];
    }
}
