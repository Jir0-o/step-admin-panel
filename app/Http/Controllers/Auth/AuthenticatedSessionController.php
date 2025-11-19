<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use App\Models\Store;
use App\Models\StoreToken;
use Carbon\Carbon;

class AuthenticatedSessionController extends Controller
{
    public function create()
    {
        return view('auth.login'); // adjust if your login view path differs
    }

    /**
     * Handle login (Fortify replacement or custom route)
     */
    public function store(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        $credentials = $request->only('email', 'password');

        // Attempt to authenticate using default guard
        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            return back()->withErrors(['email' => trans('auth.failed')]);
        }

        $request->session()->regenerate();
        $user = Auth::user();

        // Sync tokens for all configured stores (server-side stored only)
        $syncResults = $this->syncStoreTokens($user->id, $credentials['email'], $credentials['password']);

        // optional: log results for admin / debugging
        Log::info('Store tokens sync results for user '.$user->id, ['results' => $syncResults]);

        // redirect to intended page (no tokens sent to client)
        return redirect()->intended(route('dashboard.index'));
    }

    public function destroy(Request $request)
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }

    /**
     * Sync tokens for all stores that have login_api_url.
     * Tokens are saved encrypted in store_tokens table.
     *
     * @param int $userId - mother-app user id
     * @param string $email - credentials email/login
     * @param string $password - credentials password
     * @return array results keyed by store_id
     */
    protected function syncStoreTokens(int $userId, string $motherEmail, string $motherPassword): array
    {
        $results = [];
        $client = new Client([
            'timeout' => 8.0,
            'connect_timeout' => 3.0,
        ]);

        // get stores which have login_api_url configured
        $stores = Store::whereNotNull('login_api_url')
                    ->where('login_api_url', '<>', '')
                    ->get();

        // quick table existence check (defensive)
        $tableExists = true;
        try {
            if (! DB::getSchemaBuilder()->hasTable('store_tokens')) {
                $tableExists = false;
                Log::warning('syncStoreTokens: table store_tokens does not exist');
            }
        } catch (\Throwable $e) {
            $tableExists = false;
            Log::error('syncStoreTokens: schema check failed: '.$e->getMessage());
        }

        // helper to mask password for logs
        $maskPwd = function(?string $p) {
            if (!$p) return null;
            $len = strlen($p);
            if ($len <= 6) return substr($p, 0, 1) . str_repeat('*', max(0, $len-2)) . substr($p, -1);
            return substr($p, 0, 3) . '...' . substr($p, -3);
        };

        // helper: attempt safe decrypt (handles single or accidental double-encrypt)
        $safeDecrypt = function($encrypted) {
            if (empty($encrypted)) return null;
            try {
                $first = Crypt::decryptString($encrypted);
                // if result still looks like a Laravel-encrypted payload (base64 JSON starting with eyJ),
                // try decrypting second time.
                if (is_string($first) && strpos($first, 'eyJ') === 0) {
                    try {
                        return Crypt::decryptString($first);
                    } catch (\Throwable $e) {
                        // if second decrypt fails, return the first decrypted value
                        return $first;
                    }
                }
                return $first;
            } catch (\Throwable $e) {
                // if decryption fails, return null
                return null;
            }
        };

        foreach ($stores as $store) {
            $storeId = $store->id;
            $apiUrl = rtrim($store->login_api_url);

            // determine credentials (prefer store-level)
            $credEmail = $store->user_email ?: $motherEmail;

            // get store password (try safe decrypt; fallback to raw column)
            $storePwd = null;
            try {
                if (method_exists($store, 'getDecryptedPassword')) {
                    $storePwd = $store->getDecryptedPassword();
                    // if model helper returned something that looks encrypted, try safeDecrypt on raw column
                    if ($storePwd && strpos($storePwd, 'eyJ') === 0) {
                        $storePwd = $safeDecrypt($store->user_password);
                    }
                } else {
                    $storePwd = $safeDecrypt($store->user_password) ?? $store->user_password;
                }
            } catch (\Throwable $e) {
                Log::warning("syncStoreTokens: decryption failed for store {$storeId}: ".$e->getMessage());
                $storePwd = null;
            }

            $credPassword = $storePwd ?: $motherPassword;

            $singleResult = [
                'ok' => false,
                'store_id' => $storeId,
                'url' => $apiUrl,
                'used_email' => $credEmail,
                'used_password_sample' => $maskPwd($credPassword),
                'error' => null,
                'status' => null,
                'token_present' => false,
                'meta' => null,
            ];

            // Try payload variants (added login_user_name)
            $payloadVariants = [
                ['json' => ['login' => $credEmail, 'password' => $credPassword]],
                ['json' => ['email' => $credEmail, 'password' => $credPassword]],
                ['json' => ['user_email' => $credEmail, 'user_password' => $credPassword]],
                ['json' => ['username' => $credEmail, 'password' => $credPassword]],
                ['json' => ['login_user_name' => $credEmail, 'password' => $credPassword]],
                ['form_params' => ['login' => $credEmail, 'password' => $credPassword]],
            ];

            $attempted = 0;
            $tokenFound = null;
            $responseJson = null;
            $responseBody = null;
            $responseStatus = null;
            $expiresAt = null;

            foreach ($payloadVariants as $variant) {
                $attempted++;

                // prepare masked log payload
                $logPayload = [];
                foreach ($variant as $k => $v) {
                    if (is_array($v)) {
                        $tmp = $v;
                        if (isset($tmp['password'])) $tmp['password'] = $maskPwd($tmp['password']);
                        if (isset($tmp['user_password'])) $tmp['user_password'] = $maskPwd($tmp['user_password']);
                        $logPayload[$k] = $tmp;
                    } else {
                        $logPayload[$k] = $v;
                    }
                }

                Log::info("syncStoreTokens: trying payload for store {$storeId}", ['url' => $apiUrl, 'payload' => $logPayload]);

                try {
                    // do not throw on 4xx/5xx - capture response to inspect body/status
                    $options = $variant + [
                        'timeout' => 8,
                        'http_errors' => false,
                        'headers' => [
                            'Accept' => 'application/json',
                            'User-Agent' => 'MotherApp/1.0',
                        ],
                    ];

                    $resp = $client->post($apiUrl, $options);

                    $responseStatus = $resp->getStatusCode();
                    $responseBody = (string) $resp->getBody();

                    Log::info("syncStoreTokens: response for store {$storeId}", [
                        'status' => $responseStatus,
                        'body_preview' => substr($responseBody, 0, 2000)
                    ]);

                    // parse JSON if possible
                    $parsed = json_decode($responseBody, true);
                    if (json_last_error() === JSON_ERROR_NONE) {
                        $responseJson = $parsed;
                    } else {
                        $responseJson = null;
                    }

                    // token extraction
                    if (is_array($responseJson)) {
                        if (!empty($responseJson['token'])) {
                            $tokenFound = $responseJson['token'];
                        } elseif (!empty($responseJson['access_token'])) {
                            $tokenFound = $responseJson['access_token'];
                        } elseif (!empty($responseJson['data']['token'])) {
                            $tokenFound = $responseJson['data']['token'];
                        } elseif (!empty($responseJson['data']['access_token'])) {
                            $tokenFound = $responseJson['data']['access_token'];
                        } elseif (!empty($responseJson['result']['token'])) {
                            $tokenFound = $responseJson['result']['token'];
                        }

                        // expiry
                        if (empty($expiresAt)) {
                            if (!empty($responseJson['expires_in']) && is_numeric($responseJson['expires_in'])) {
                                $expiresAt = Carbon::now()->addSeconds((int)$responseJson['expires_in']);
                            } elseif (!empty($responseJson['expires_at'])) {
                                try {
                                    $expiresAt = Carbon::parse($responseJson['expires_at']);
                                } catch (\Throwable $e) { /* ignore */ }
                            }
                        }
                    }

                    // fallback: plain token string
                    if (!$tokenFound && $responseStatus === 200) {
                        $trim = trim($responseBody);
                        if (preg_match('/^[A-Za-z0-9\-\._\|]+$/', $trim)) {
                            $tokenFound = $trim;
                        }
                    }

                    // helpful error message if 401 with JSON message
                    if ($responseStatus === 401 && $responseJson) {
                        $singleResult['error'] = 'Remote 401: ' . ($responseJson['message'] ?? json_encode($responseJson));
                    }

                    if ($tokenFound) break;
                } catch (RequestException $re) {
                    Log::warning("Store login request failed for store {$storeId}: " . $re->getMessage());
                    $singleResult['error'] = 'RequestException: ' . $re->getMessage();
                    // continue trying other variants
                } catch (\Throwable $e) {
                    Log::error("Store login unexpected error for store {$storeId}: " . $e->getMessage());
                    $singleResult['error'] = 'Exception: ' . $e->getMessage();
                    // continue trying other variants
                }
            } // end payload loop

            $singleResult['attempts'] = $attempted;
            $singleResult['status'] = $responseStatus;
            $singleResult['raw_response'] = $responseJson ?? substr($responseBody ?? '', 0, 2000);

            if ($tokenFound) {
                // store encrypted token (same as before)
                try {
                    $encrypted = Crypt::encryptString($tokenFound);
                } catch (\Throwable $e) {
                    Log::error("Encrypt token failed for store {$storeId}: " . $e->getMessage());
                    $singleResult['ok'] = false;
                    $singleResult['error'] = 'Encryption failed';
                    $results[$storeId] = $singleResult;
                    continue;
                }

                if (! $tableExists) {
                    $singleResult['ok'] = false;
                    $singleResult['error'] = 'store_tokens table missing';
                    $results[$storeId] = $singleResult;
                    continue;
                }

                // Persist (Eloquent first, DB fallback)
                $saved = false;
                try {
                    Log::info("Attempting to save token for store {$storeId}, user {$userId}");
                    try {
                        StoreToken::updateOrCreate(
                            ['store_id' => $storeId, 'user_id' => $userId],
                            [
                                'token' => $encrypted,
                                'meta' => json_encode([
                                    'response' => $responseJson,
                                    'raw' => substr($responseBody ?? '', 0, 2000),
                                ]),
                                'expires_at' => $expiresAt ? $expiresAt->toDateTimeString() : null,
                                'created_at' => Carbon::now()->toDateTimeString(),
                            ]
                        );
                        $saved = true;
                        Log::info("StoreToken saved (model) for store {$storeId}, user {$userId}");
                    } catch (\Throwable $e) {
                        Log::warning("StoreToken model save failed for store {$storeId}, user {$userId}: ".$e->getMessage());
                        $saved = false;
                    }

                    if (! $saved) {
                        $now = Carbon::now()->toDateTimeString();
                        DB::table('store_tokens')->updateOrInsert(
                            ['store_id' => $storeId, 'user_id' => $userId],
                            [
                                'token' => $encrypted,
                                'meta' => json_encode([
                                    'response' => $responseJson,
                                    'raw' => substr($responseBody ?? '', 0, 2000),
                                ]),
                                'expires_at' => $expiresAt ? $expiresAt->toDateTimeString() : null,
                                'created_at' => $now,
                            ]
                        );
                        $saved = true;
                        Log::info("StoreToken saved (DB) for store {$storeId}, user {$userId}");
                    }
                } catch (\Throwable $e) {
                    Log::error("StoreToken save failed for store {$storeId}, user {$userId}: " . $e->getMessage());
                    $singleResult['ok'] = false;
                    $singleResult['error'] = 'DB save failed: '.$e->getMessage();
                    $results[$storeId] = $singleResult;
                    continue;
                }

                if ($saved) {
                    $singleResult['ok'] = true;
                    $singleResult['token_present'] = true;
                    $singleResult['token_sample'] = substr($tokenFound, 0, 6) . '...';
                }
            } else {
                $singleResult['ok'] = false;
                $singleResult['token_present'] = false;
                if (empty($singleResult['error'])) {
                    $singleResult['error'] = 'No token found in response';
                }
            }

            $results[$storeId] = $singleResult;
        } // end foreach stores

        return $results;
    }

    /**
     * Helper: get decrypted token for a given store and user
     * Returns null if not present or decryption fails.
     */
    protected function getStoreToken(int $storeId, int $userId): ?string
    {
        try {
            $row = StoreToken::where('store_id', $storeId)
                        ->where('user_id', $userId)
                        ->latest('created_at')
                        ->first();
        } catch (\Throwable $e) {
            Log::warning("getStoreToken: model query failed: ".$e->getMessage());
            // fallback to DB select
            $row = DB::table('store_tokens')
                    ->where('store_id', $storeId)
                    ->where('user_id', $userId)
                    ->orderByDesc('created_at')
                    ->first();
        }

        if (! $row || empty($row->token)) {
            return null;
        }

        try {
            return Crypt::decryptString($row->token);
        } catch (\Throwable $e) {
            Log::warning("Decrypting store token failed (store {$storeId}, user {$userId}): " . $e->getMessage());
            return null;
        }
    }

}
