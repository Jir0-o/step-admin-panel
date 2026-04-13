<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Store;
use App\Models\StoreRoute;
use App\Models\StoreToken;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;

class SalesDataController extends Controller
{
    /**
     * Display sales report page
     */
    public function index(Store $store)
    {
        return view('backend.admin.sales-data', compact('store'));
    }

    /**
     * Get sales data from POS
     */
    public function getSalesData(Request $request, Store $store)
    {
        Log::info('==== getSalesData START ====', ['store_id' => $store->id, 'request' => $request->all()]);

        $route = StoreRoute::where('store_id', $store->id)
            ->where('endpoint', 'api/manager/data/sales-data')
            ->where('is_active', true)
            ->first();

        if (!$route || !$route->base_url) {
            Log::warning('Sales endpoint not configured', ['store_id' => $store->id]);
            return response()->json(['ok' => false, 'message' => 'Sales data endpoint not configured for this store'], 400);
        }

        $tokenRow = StoreToken::where('store_id', $store->id)
            ->where('user_id', Auth::id())
            ->latest('created_at')->first();
        if (!$tokenRow || empty($tokenRow->token)) {
            Log::warning('No token for store', ['store_id' => $store->id]);
            return response()->json(['ok' => false, 'message' => 'No API token available'], 401);
        }

        try {
            $token = Crypt::decryptString($tokenRow->token);
            $posUrl = rtrim($route->base_url, '/') . '/' . ltrim($route->endpoint, '/');

            Log::info('Calling POS for sales data', ['pos_url' => $posUrl]);

            // Get request parameters
            $start = max(0, (int)$request->input('start', 0));
            $length = max(1, (int)$request->input('length', 100));
            $draw = (int)$request->input('draw', 1);
            $page = (int) floor($start / $length) + 1;

            // Build query for POS API
            $query = [
                'page'     => $page,
                'per_page' => $length,
            ];

            // Date filters
            if ($request->has('from_date') && !empty($request->input('from_date'))) {
                $query['from_date'] = $request->input('from_date');
            }
            
            if ($request->has('to_date') && !empty($request->input('to_date'))) {
                $query['to_date'] = $request->input('to_date');
            }

            // Handle search
            if ($request->has('search.value') && !empty($request->input('search.value'))) {
                $query['search'] = $request->input('search.value');
            } elseif ($request->has('search_sales') && !empty($request->input('search_sales'))) {
                $query['search'] = $request->input('search_sales');
            }

            // Handle ordering
            if ($request->has('order.0.column') && $request->has('order.0.dir')) {
                $columnIndex = (int)$request->input('order.0.column');
                $orderDir = $request->input('order.0.dir', 'desc');
                
                // Map DataTables column index to actual database columns
                $columnMap = [
                    0  => 'cart_id',
                    1  => 'cart_date',
                    2  => 'customer_mobile',
                    3  => 'payment_method',
                    4  => 'total_amount',
                    5  => 'vat_amount',
                    6  => 'discount',
                    7  => 'paid_amount',
                    8  => 'due_amount',
                    9  => 'gross_profit',
                    10 => 'net_profit',
                    11 => 'created_by',
                    12 => 'waiter_name',
                ];
                
                if (isset($columnMap[$columnIndex])) {
                    $query['order_by'] = $columnMap[$columnIndex];
                    $query['order_dir'] = in_array(strtolower($orderDir), ['asc','desc']) ? $orderDir : 'desc';
                }
            }

            Log::info('POS sales query params', ['query' => $query]);

            // Make API call to POS
            $response = Http::withToken($token)
                ->timeout(60)
                ->acceptJson()
                ->get($posUrl, array_filter($query));

            Log::info('POS HTTP status for sales', ['status' => $response->status()]);

            if (!$response->successful()) {
                Log::error('POS returned non-success for sales', ['status' => $response->status(), 'body' => $response->body()]);
                return response()->json([
                    'draw' => $draw,
                    'recordsTotal' => 0,
                    'recordsFiltered' => 0,
                    'data' => [],
                    'error' => 'Failed to fetch sales data from POS: ' . $response->status()
                ], 502);
            }

            $body = $response->json();
            
            if (!isset($body['ok']) || $body['ok'] !== true) {
                Log::warning('POS returned ok:false for sales', ['body' => $body]);
                return response()->json([
                    'draw' => $draw,
                    'recordsTotal' => 0,
                    'recordsFiltered' => 0,
                    'data' => [],
                    'error' => $body['message'] ?? 'POS returned error for sales data'
                ], 422);
            }

            // Extract data from response
            $pageData = $body['data'] ?? [];
            $rows = $pageData['data'] ?? [];
            $total = (int) ($pageData['total'] ?? 0);
            $perPage = (int) ($pageData['per_page'] ?? $length);
            $currentPage = (int) ($pageData['current_page'] ?? $page);
            $lastPage = (int) ($pageData['last_page'] ?? 1);

            // Transform rows for display
            $transformed = [];
            foreach ($rows as $r) {
                // Format items for display
                $itemsText = '';
                if (!empty($r['items'])) {
                    $itemCount = count($r['items']);
                    $displayItems = array_slice($r['items'], 0, 3);
                    
                    foreach ($displayItems as $item) {
                        $itemsText .= $item['barcode'] . ' (' . $item['quantity'] . ') ' . $item['brand'] . '<br>';
                    }
                    
                    if ($itemCount > 3) {
                        $itemsText .= '<small class="text-muted">+' . ($itemCount - 3) . ' more items</small>';
                    }
                }
                
                $transformed[] = [
                    'DT_RowId' => 'sale_' . ($r['cart_id'] ?? uniqid()),
                    'cart_id' => $r['cart_id'] ?? null,
                    'trx_number' => $r['trx_number'] ?? '',
                    'cart_date' => $r['cart_date'] ?? '',
                    'customer_mobile' => $r['customer_mobile'] ?? '',
                    'payment_method' => $r['payment_method'] ?? '',
                    'total_amount' => (float) ($r['total_cart_amount'] ?? 0),
                    'vat_amount' => (float) ($r['vat_amount'] ?? 0),
                    'discount' => (float) ($r['total_discount'] ?? 0),
                    'paid_amount' => (float) ($r['paid_amount'] ?? 0),
                    'due_amount' => (float) ($r['due_amount'] ?? 0),
                    'gross_profit' => (float) ($r['gross_profit'] ?? 0),
                    'net_profit' => (float) ($r['net_profit'] ?? 0),
                    'created_by' => $r['created_by'] ?? '',
                    'waiter_name' => $r['waiter_name'] ?? '',
                    'table_no' => $r['table_no'] ?? '',
                    'items_html' => $itemsText,
                    'items_count' => count($r['items'] ?? []),
                ];
            }

            // Get summaries
            $allDataSummary = $body['data']['summary'] ?? [];
            $currentPageSummary = $body['data']['page_summary'] ?? [];

            $payload = [
                'draw' => $draw,
                'recordsTotal' => $total,
                'recordsFiltered' => $total,
                'data' => $transformed,
                'all_data_summary' => $allDataSummary, // For TOP cards
                'current_page_summary' => $currentPageSummary, // For FOOTER
                'pagination' => [
                    'current_page' => $currentPage,
                    'per_page' => $perPage,
                    'total' => $total,
                    'last_page' => $lastPage,
                ]
            ];

            Log::info('==== getSalesData END ====', [
                'returned_rows' => count($transformed),
                'total_items' => $total,
                'all_data_summary' => $allDataSummary
            ]);

            return response()->json($payload, 200);

        } catch (\Throwable $e) {
            Log::error('Sales data fetch error', ['msg' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return response()->json([
                'draw' => $draw ?? 1,
                'recordsTotal' => 0,
                'recordsFiltered' => 0,
                'data' => [],
                'error' => 'Server error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Export sales data as CSV
     */
    public function exportCsv(Request $request, Store $store)
    {
        // Get active route
        $route = StoreRoute::where('store_id', $store->id)
            ->where('endpoint', 'api/manager/data/export-sales-data')
            ->first();

        if (!$route || !$route->base_url) {
            abort(404, 'Sales data export endpoint not configured');
        }

        // Get API token
        $tokenRow = StoreToken::where('store_id', $store->id)->latest('created_at')->first();
        if (!$tokenRow || empty($tokenRow->token)) {
            abort(401, 'No API token available');
        }

        try {
            $token = Crypt::decryptString($tokenRow->token);
            $posUrl = rtrim($route->base_url, '/') . '/' . ltrim($route->endpoint, '/');
            
            // Build query with filters
            $query = [];
            
            if ($request->has('from_date') && !empty($request->input('from_date'))) {
                $query['from_date'] = $request->input('from_date');
            }
            
            if ($request->has('to_date') && !empty($request->input('to_date'))) {
                $query['to_date'] = $request->input('to_date');
            }
            
            if ($request->has('search') && !empty($request->input('search'))) {
                $query['search'] = $request->input('search');
            }

            $response = Http::withToken($token)
                ->timeout(120)
                ->acceptJson()
                ->get($posUrl, $query);

            if ($response->successful()) {
                $filename = 'sales_report_' . $store->name . '_' . date('Y-m-d_H-i-s') . '.csv';
                
                return response($response->body(), 200, [
                    'Content-Type' => 'text/csv; charset=utf-8',
                    'Content-Disposition' => 'attachment; filename="' . $filename . '"',
                ]);
            } else {
                abort(500, 'Failed to fetch export data from POS');
            }

        } catch (\Throwable $e) {
            Log::error('Sales data export error: ' . $e->getMessage());
            abort(500, 'Export failed: ' . $e->getMessage());
        }
    }
}