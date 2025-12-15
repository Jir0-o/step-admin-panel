<?php

namespace App\Http\Controllers;

use App\Models\Store;
use App\Models\StoreRoute;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use App\Services\StoreTokenSyncService;
use App\Models\StoreToken;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Http;

class StoreController extends Controller
{
    public function index(Request $request)
    {
        try {
            $stores = Store::orderBy('id')->get();

            if ($request->ajax() || $request->wantsJson() || $request->header('Accept') === 'application/json') {
                $stores = $stores->map(function ($s) {
                    return [
                        'id' => $s->id,
                        'name' => $s->name,
                        'base_url' => $s->base_url,
                        'login_api_url' => $s->login_api_url,
                        'user_email' => $s->user_email,
                        'created_at' => $s->created_at,
                        'updated_at' => $s->updated_at,
                    ];
                });
                return response()->json(['ok' => true, 'data' => $stores], 200);
            }

            return view('backend.admin.store-create');
        } catch (\Throwable $e) {
            Log::error('Store index error: '.$e->getMessage());
            return $request->wantsJson()
                ? response()->json(['ok' => false, 'message' => 'Failed to load stores'], 500)
                : view('backend.admin.store-create', ['error' => 'Failed to load stores']);
        }
    }


    public function store(Request $request)
    {
        try {
            $data = $request->validate([
                'name' => 'required|string|max:255',
                'base_url' => 'nullable|url|max:255',
                'login_api_url' => 'nullable|url|max:255',
                'user_email' => 'nullable|email|max:255',
                'user_password' => 'nullable|string|min:1|max:255',
            ]);

            // IMPORTANT: do NOT encrypt here — model mutator will encrypt once
            // If user_password empty, leave as null so mutator won't set it
            if (empty($data['user_password'])) {
                $data['user_password'] = null;
            }

            $store = Store::create($data);

            return response()->json([
                'ok' => true,
                'data' => [
                    'id' => $store->id,
                    'name' => $store->name,
                    'base_url' => $store->base_url,
                    'login_api_url' => $store->login_api_url,
                    'user_email' => $store->user_email,
                ],
                'message' => 'Store created'
            ], 201);
        } catch (ValidationException $ve) {
            return response()->json(['ok' => false, 'message' => 'Validation failed', 'errors' => $ve->errors()], 422);
        } catch (\Throwable $e) {
            Log::error('Store create error: '.$e->getMessage() ."\n".$e->getTraceAsString());
            return response()->json(['ok' => false, 'message' => 'Failed to create store', 'error' => $e->getMessage()], 500);
        }
    }

    public function show(Store $store)
    {
        try {
            // Do NOT include decrypted password in API response for security.
            return response()->json([
                'ok' => true,
                'data' => [
                    'id' => $store->id,
                    'name' => $store->name,
                    'base_url' => $store->base_url,
                    'login_api_url' => $store->login_api_url,
                    'user_email' => $store->user_email,
                    // do not send user_password
                ]
            ], 200);
        } catch (\Throwable $e) {
            Log::error('Store show error: '.$e->getMessage());
            return response()->json(['ok' => false, 'message' => 'Failed to load store'], 500);
        }
    }

    public function update(Request $request, Store $store)
    {
        try {
            $data = $request->validate([
                'name' => 'required|string|max:255',
                'base_url' => 'nullable|url|max:255',
                'login_api_url' => 'nullable|url|max:255',
                'user_email' => 'nullable|email|max:255',
                'user_password' => 'nullable|string|min:1|max:255',
            ]);

            // Do not encrypt here. If user_password present, model mutator will encrypt.
            if (empty($data['user_password'])) {
                // don't change existing password if empty
                unset($data['user_password']);
            }

            $store->update($data);

            return response()->json([
                'ok' => true,
                'data' => [
                    'id' => $store->id,
                    'name' => $store->name,
                    'base_url' => $store->base_url,
                    'login_api_url' => $store->login_api_url,
                    'user_email' => $store->user_email,
                ],
                'message' => 'Store updated'
            ], 200);
        } catch (ValidationException $ve) {
            return response()->json(['ok' => false, 'message' => 'Validation failed', 'errors' => $ve->errors()], 422);
        } catch (\Throwable $e) {
            Log::error('Store update error: '.$e->getMessage());
            return response()->json(['ok' => false, 'message' => 'Failed to update store'], 500);
        }
    }

    public function destroy(Store $store)
    {
        try {
            $store->delete();
            return response()->json(['ok' => true, 'message' => 'Store deleted'], 200);
        } catch (\Throwable $e) {
            Log::error('Store delete error: '.$e->getMessage());
            return response()->json(['ok' => false, 'message' => 'Failed to delete store'], 500);
        }
    }

    public function sync(
        Request $request,
        StoreTokenSyncService $service
    ) {
        $userId = auth()->id();

        $results = $service->syncForUser($userId);

        $expiring = StoreToken::where('user_id', $userId)
            ->whereNotNull('expires_at')
            ->get()
            ->filter(fn ($t) => $t->isNearExpiry())
            ->map(fn ($t) => [
                'store_id' => $t->store_id,
                'expires_at' => $t->expires_at->toDateTimeString(),
            ])
            ->values();

        return response()->json([
            'ok' => true,
            'synced_at' => now()->toDateTimeString(),
            'results' => $results,
            'expiring_tokens' => $expiring,
        ]);
    }

    public function showDetails($storeId)
    {
        $store = Store::findOrFail($storeId);

        if (!Auth::check()) {
            return redirect()->route('login');
        }

        // Get latest token
        $tokenRow = StoreToken::where('store_id', $storeId)
            ->latest()
            ->first();

        if (!$tokenRow) {
            return back()->with('error', 'Store token missing');
        }

        try {
            $token = Crypt::decryptString($tokenRow->token);
        } catch (\Throwable $e) {
            return back()->with('error', 'Invalid store token');
        }

        // Resolve summary route STRICTLY from store_routes
        $route = StoreRoute::where('store_id', $storeId)
            ->where('endpoint', 'summary')
            ->where('is_active', true)
            ->first();

        if (!$route) {
            return back()->with('error', 'Summary API route not configured');
        }

        // URL already includes /summary at the end
        $url = rtrim($route->base_url, '/');
        
        // Prepare tables as comma-separated string (as expected by the API)
        $tables = 'products,suppliers,cart_informtion,expense_details,banner_information,customer_payments,customer_payment_infos,expenses,purchase_info,purchase_details,supplier_payments,cart_items,final_stock_table';

        try {
            // First try POST (as in your fetchSummary method)
            $client = new \GuzzleHttp\Client([
                'timeout' => 20,
                'http_errors' => false,
            ]);

            $resp = $client->post($url, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $token,
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                ],
                'json' => ['tables' => $tables], // Send as JSON with tables string
            ]);

            // If POST fails, try GET fallback
            if ($resp->getStatusCode() >= 400) {
                $urlWithParams = $url . '?' . http_build_query(['tables' => $tables]);
                $resp = $client->get($urlWithParams, [
                    'headers' => [
                        'Authorization' => 'Bearer ' . $token,
                        'Accept' => 'application/json',
                    ],
                ]);
            }

            if ($resp->getStatusCode() >= 200 && $resp->getStatusCode() < 300) {
                $data = json_decode($resp->getBody(), true);
                
                // Check if response has the expected structure
                if (isset($data['ok']) && $data['ok'] && isset($data['results'])) {
                    $results = $data['results'];
                    
                    // Prepare summary data for the view
                    $summary = [
                        'cart' => $results['cart_informtion'] ?? [],
                        'expenses' => $results['expense_details'] ?? [],
                        'products' => $results['products'] ?? [],
                        'suppliers' => $results['suppliers'] ?? [],
                        'banner' => $results['banner_information'] ?? [],
                        'others' => collect($results)->except([
                            'cart_informtion',
                            'expense_details',
                            'products',
                            'suppliers',
                            'banner_information',
                        ])->toArray(),
                    ];

                    return view('backend.admin.storeDetails', [
                        'store' => $store,
                        'summary' => $summary,
                        'data' => $data, // Pass full data for raw view
                    ]);
                } else {
                    // Handle unexpected response structure
                    return view('backend.admin.storeDetails', [
                        'store' => $store,
                        'error' => 'Unexpected API response format',
                        'data' => $data, // Still pass data for debugging
                    ]);
                }
            } else {
                $errorBody = json_decode($resp->getBody(), true);
                $errorMessage = $errorBody['message'] ?? 'API returned status: ' . $resp->getStatusCode();
                
                return view('backend.admin.storeDetails', [
                    'store' => $store,
                    'error' => $errorMessage,
                    'status' => $resp->getStatusCode(),
                ]);
            }

        } catch (\Throwable $e) {
            Log::error('Store details error: ' . $e->getMessage());
            
            return view('backend.admin.storeDetails', [
                'store' => $store,
                'error' => 'Failed to connect to POS: ' . $e->getMessage(),
            ]);
        }
    }
}
