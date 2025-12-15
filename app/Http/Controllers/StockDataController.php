<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Store;
use App\Models\StoreRoute;
use App\Models\StoreToken;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class StockDataController extends Controller
{
    /**
     * Display stock data page
     */
    public function index(Store $store)
    {
        return view('backend.admin.stock-data', compact('store'));
    }

    /**
     * Get stock data via AJAX for DataTable
     */
    public function getStockData(Request $request, Store $store)
    {
        // Validate route config
        $route = StoreRoute::where('store_id', $store->id)
            ->where('endpoint', 'api/manager/data/final-stock-data')
            ->where('is_active', true)
            ->first();

        if (!$route || !$route->base_url) {
            return response()->json([
                'draw' => (int)$request->input('draw', 0),
                'recordsTotal' => 0,
                'recordsFiltered' => 0,
                'data' => [],
                'error' => 'Store route not configured'
            ]);
        }

        // Token
        $tokenRow = StoreToken::where('store_id', $store->id)->latest('created_at')->first();
        if (!$tokenRow || empty($tokenRow->token)) {
            return response()->json([
                'draw' => (int)$request->input('draw', 0),
                'recordsTotal' => 0,
                'recordsFiltered' => 0,
                'data' => [],
                'error' => 'No API token available'
            ]);
        }

        try {
            $token = Crypt::decryptString($tokenRow->token);
            $posUrl = rtrim($route->base_url, '/') . '/' . ltrim($route->endpoint, '/');

            // DataTables paging
            $start = max(0, (int)$request->input('start', 0));
            $length = max(1, (int)$request->input('length', 25));
            $page = (int) floor($start / $length) + 1;

            // Prepare query for POS
            $query = [
                'page' => $page,
                'per_page' => $length,
                'store_id' => $store->id,
            ];

            // Pass search (POS must support it; otherwise ignored)
            if ($search = trim($request->input('search.value') ?? $request->input('search_product') ?? '')) {
                $query['search'] = $search;
            }

            // Map ordering to safe column names (DataTables sends column index)
            $columnIndex = $request->input('order.0.column');
            $orderDir = $request->input('order.0.dir', 'desc');

            // Allowed mapping from DataTables column index to POS column name
            $columnMap = [
                0 => 'stock_id',
                1 => 'article',
                2 => 'product_id',
                3 => 'barcode',
                4 => 'colors_id',
                5 => 'final_quantity',
                6 => 'purchase_price',
                7 => null, // computed total_value
                8 => 'in_order_queue',
                9 => 'sync_status',
            ];

            if (is_numeric($columnIndex) && isset($columnMap[(int)$columnIndex]) && $columnMap[(int)$columnIndex]) {
                $query['order_by'] = $columnMap[(int)$columnIndex];
                $query['order_dir'] = in_array(strtolower($orderDir), ['asc','desc']) ? $orderDir : 'desc';
            }

            // Forward custom filters
            if ($filterCategory = $request->input('filter_category')) $query['filter_category'] = $filterCategory;
            if ($filterStatus = $request->input('filter_status')) $query['filter_status'] = $filterStatus;

            $response = Http::withToken($token)
                ->timeout(60)
                ->acceptJson()
                ->get($posUrl, array_filter($query));

            if (! $response->successful()) {
                return response()->json([
                    'draw' => (int)$request->input('draw', 0),
                    'recordsTotal' => 0,
                    'recordsFiltered' => 0,
                    'data' => [],
                    'error' => 'Failed to fetch data from POS: ' . $response->status()
                ]);
            }

            $body = $response->json();

            if (!($body['ok'] ?? false)) {
                return response()->json([
                    'draw' => (int)$request->input('draw', 0),
                    'recordsTotal' => 0,
                    'recordsFiltered' => 0,
                    'data' => [],
                    'error' => $body['message'] ?? 'POS returned error'
                ]);
            }

            $pageData = $body['data'] ?? [];
            $rows = $pageData['data'] ?? [];
            $total = (int) ($pageData['total'] ?? count($rows));

            return response()->json([
                'draw' => (int)$request->input('draw', 0),
                'recordsTotal' => $total,
                'recordsFiltered' => $total,
                'data' => $rows,
                'current_page' => (int)($pageData['current_page'] ?? $page),
                'last_page' => (int)($pageData['last_page'] ?? ceil($total / max(1,(int)$pageData['per_page'] ?? $length))),
            ]);

        } catch (\Throwable $e) {
            Log::error('Stock data fetch error: ' . $e->getMessage());
            return response()->json([
                'draw' => (int)$request->input('draw', 0),
                'recordsTotal' => 0,
                'recordsFiltered' => 0,
                'data' => [],
                'error' => 'Server error'
            ]);
        }
    }


    /**
     * Export stock data as CSV
     */
    public function exportCsv(Request $request, Store $store)
    {
        // Get active route
        $route = StoreRoute::where('store_id', $store->id)
            ->where('endpoint', 'api/manager/data/final-stock-data')
            ->where('is_active', true)
            ->first();

        if (!$route || !$route->base_url) {
            abort(404, 'Store route not configured');
        }

        // Get API token
        $tokenRow = StoreToken::where('store_id', $store->id)->latest('created_at')->first();
        if (!$tokenRow || empty($tokenRow->token)) {
            abort(401, 'No API token available');
        }

        try {
            $token = Crypt::decryptString($tokenRow->token);
            $posUrl = rtrim($route->base_url, '/') . '/' . ltrim($route->endpoint, '/');
            
            // Fetch all data (you might want to implement pagination for large exports)
            $query = [
                'per_page' => 5000, // Adjust based on your needs
                'store_id' => $store->id,
            ];

            $response = Http::withToken($token)
                ->timeout(120)
                ->acceptJson()
                ->get($posUrl, $query);

            if ($response->successful()) {
                $data = $response->json();
                
                if ($data['ok'] ?? false) {
                    $stockData = $data['data']['data'] ?? [];
                    
                    $filename = 'stock_data_' . $store->name . '_' . date('Y-m-d') . '.csv';
                    
                    $headers = [
                        'Content-Type' => 'text/csv',
                        'Content-Disposition' => 'attachment; filename="' . $filename . '"',
                    ];

                    $callback = function() use ($stockData) {
                        $file = fopen('php://output', 'w');
                        
                        // Add headers
                        if (!empty($stockData)) {
                            fputcsv($file, array_keys($stockData[0]));
                        }
                        
                        // Add data
                        foreach ($stockData as $row) {
                            fputcsv($file, $row);
                        }
                        
                        fclose($file);
                    };

                    return response()->stream($callback, 200, $headers);
                }
            }

            abort(500, 'Failed to export data');

        } catch (\Throwable $e) {
            Log::error('Stock data export error: ' . $e->getMessage());
            abort(500, 'Export failed: ' . $e->getMessage());
        }
    }
}