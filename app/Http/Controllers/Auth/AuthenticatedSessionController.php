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
    protected function syncStoreTokens(
        int $userId,
        string $motherEmail,
        string $motherPassword
    ): array {

        $results = [];

        $client = new Client([
            'timeout' => 8,
            'connect_timeout' => 3,
            'http_errors' => false,
        ]);

        $stores = Store::whereNotNull('login_api_url')
            ->where('login_api_url', '!=', '')
            ->get();

        $mask = fn($p) => $p ? substr($p, 0, 3).'***'.substr($p, -2) : null;

        foreach ($stores as $store) {

            $email = $store->user_email ?: $motherEmail;

            // decrypt store password if exists, else use mother password
            $password = $store->user_password
                ? Crypt::decryptString($store->user_password)
                : $motherPassword;

            $result = [
                'store_id' => $store->id,
                'ok' => false,
                'token_present' => false,
                'used_email' => $email,
                'used_password_sample' => $mask($password),
                'error' => null,
            ];

            try {
                Log::info("syncStoreTokens: login {$store->login_api_url}", [
                    'email' => $email,
                    'password' => $mask($password),
                ]);

                $resp = $client->post($store->login_api_url, [
                    'json' => [
                        'login'    => $email,
                        'password' => $password,
                    ],
                    'headers' => [
                        'Accept' => 'application/json',
                        'User-Agent' => 'MotherApp/1.0',
                    ],
                ]);

                $status = $resp->getStatusCode();
                $body   = json_decode((string)$resp->getBody(), true);

                if ($status !== 200 || empty($body['token'])) {
                    $result['error'] = $body['message'] ?? 'Token not returned';
                    $results[$store->id] = $result;
                    continue;
                }

                // encrypt & store token
                StoreToken::updateOrCreate(
                    [
                        'store_id' => $store->id,
                        'user_id'  => $userId,
                    ],
                    [
                        'token'      => Crypt::encryptString($body['token']),
                        'expires_at' => isset($body['expires_in'])
                            ? now()->addSeconds((int)$body['expires_in'])
                            : null,
                    ]
                );

                $result['ok'] = true;
                $result['token_present'] = true;

            } catch (\Throwable $e) {
                Log::error("syncStoreTokens failed for store {$store->id}", [
                    'error' => $e->getMessage()
                ]);
                $result['error'] = $e->getMessage();
            }

            $results[$store->id] = $result;
        }

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
                        ->latest('created_at')
                        ->first();
        } catch (\Throwable $e) {
            Log::warning("getStoreToken: model query failed: ".$e->getMessage());
            // fallback to DB select
            $row = DB::table('store_tokens')
                    ->where('store_id', $storeId)
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
