<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Store;
use App\Models\StoreRoute;
use App\Services\StoreTokenService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class StockDataController extends Controller
{
    public function __construct(private readonly StoreTokenService $tokenService)
    {
    }

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
        Log::info('==== getStockData START ====', ['store_id' => $store->id, 'request' => $request->all()]);

        $route = StoreRoute::where('store_id', $store->id)
            ->where('endpoint', 'api/manager/data/final-stock-data')
            ->where('is_active', true)
            ->first();

        if (!$route || !$route->base_url) {
            Log::warning('Stock endpoint not configured', ['store_id' => $store->id]);
            return response()->json(['ok' => false, 'message' => 'Stock data endpoint not configured for this store'], 400);
        }

        try {
            $posUrl = rtrim($route->base_url, '/') . '/' . ltrim($route->endpoint, '/');

            Log::info('Calling POS', ['pos_url' => $posUrl]);

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

            // Handle search
            if ($request->has('search.value') && !empty($request->input('search.value'))) {
                $query['search'] = $request->input('search.value');
            } elseif ($request->has('search_product') && !empty($request->input('search_product'))) {
                $query['search'] = $request->input('search_product');
            }

            // Handle ordering
            if ($request->has('order.0.column') && $request->has('order.0.dir')) {
                $columnIndex = (int)$request->input('order.0.column');
                $orderDir = $request->input('order.0.dir', 'desc');
                
                $columnMap = [
                    0  => 'stock_id',
                    1  => 'product_material_name',
                    2  => 'barcode',
                    3  => 'foot_ware_categories_name',
                    4  => 'type_name',
                    5  => 'material_type_name',
                    6  => 'brand_type_name',
                    7  => 'colors_name',
                    8  => 'size_name',
                    9  => 'total_purchased_quantity',
                    10 => 'total_sold_quantity',
                    11 => 'final_quantity',
                    12 => 'sales_price',
                ];
                
                if (isset($columnMap[$columnIndex])) {
                    $query['order_by'] = $columnMap[$columnIndex];
                    $query['order_dir'] = in_array(strtolower($orderDir), ['asc','desc']) ? $orderDir : 'desc';
                }
            }

            Log::info('POS query params', ['query' => $query]);

            // Make API call to POS for paginated data
            $response = $this->tokenService->sendAuthorized(
                $store,
                (int) Auth::id(),
                'GET',
                $posUrl,
                [
                    'timeout' => 60,
                    'query' => array_filter($query),
                ]
            );

            Log::info('POS HTTP status', ['status' => $response->status()]);

            if (!$response->successful()) {
                Log::error('POS returned non-success', ['status' => $response->status(), 'body' => $response->body()]);
                return response()->json([
                    'draw' => $draw,
                    'recordsTotal' => 0,
                    'recordsFiltered' => 0,
                    'data' => [],
                    'error' => 'Failed to fetch data from POS: ' . $response->status()
                ], 502);
            }

            $body = $response->json();
            
            if (!isset($body['ok']) || $body['ok'] !== true) {
                Log::warning('POS returned ok:false', ['body' => $body]);
                return response()->json([
                    'draw' => $draw,
                    'recordsTotal' => 0,
                    'recordsFiltered' => 0,
                    'data' => [],
                    'error' => $body['message'] ?? 'POS returned error'
                ], 422);
            }

            // Extract data from response
            $pageData = $body['data'] ?? [];
            $rows = $pageData['data'] ?? [];
            $total = (int) ($pageData['total'] ?? 0);
            $perPage = (int) ($pageData['per_page'] ?? $length);
            $currentPage = (int) ($pageData['current_page'] ?? $page);
            $lastPage = (int) ($pageData['last_page'] ?? 1);

            // Get summary from POS response (THIS IS FOR ALL DATA)
            $allDataSummary = [
                'total_items' => $total,
                'total_quantity' => 0,
                'total_value' => 0,
                'total_purchased' => 0,
                'total_sold' => 0,
            ];

            if (isset($body['data']['summary'])) {
                $summaryData = $body['data']['summary'];
                $allDataSummary = [
                    'total_items' => $summaryData['total_items'] ?? $total,
                    'total_quantity' => $summaryData['total_quantity'] ?? 0,
                    'total_value' => $summaryData['total_value'] ?? 0,
                    'total_purchased' => $summaryData['total_purchased'] ?? 0,
                    'total_sold' => $summaryData['total_sold'] ?? 0,
                ];
            }

            // Get filters from request for local filtering
            $categoryFilter = $request->input('filter_category');
            $typeFilter = $request->input('filter_type');
            $stockStatusFilter = $request->input('filter_status');

            // Transform rows for display and apply local filtering
            $transformed = [];
            $currentPageData = []; // Data for current page only
            $currentPageSummary = [ // Summary for current page only
                'total_quantity' => 0,
                'total_value' => 0,
                'total_purchased' => 0,
                'total_sold' => 0,
                'total_items' => 0,
            ];
            
            foreach ($rows as $r) {
                $stockQty = $r['final_quantity'] ?? 0;
                $unitPrice = $r['sales_price'] ?? ($r['purchase_price'] ?? 0);
                
                $item = [
                    'DT_RowId' => 'row_' . ($r['stock_id'] ?? uniqid()),
                    'stock_id' => $r['stock_id'] ?? null,
                    'product_material_name' => $r['product_material_name'] ?? ($r['product_name'] ?? 'N/A'),
                    'article' => $r['article'] ?? '',
                    'barcode' => $r['barcode'] ?? '',
                    'foot_ware_categories_name' => $r['foot_ware_categories_name'] ?? '',
                    'type_name' => $r['type_name'] ?? '',
                    'material_type_name' => $r['material_type_name'] ?? '',
                    'brand_type_name' => $r['brand_type_name'] ?? '',
                    'colors_name' => $r['colors_name'] ?? '',
                    'size_name' => $r['size_name'] ?? '',
                    'total_purchased_quantity' => $r['total_purchased_quantity'] ?? 0,
                    'total_sold_quantity' => $r['total_sold_quantity'] ?? 0,
                    'final_quantity' => $stockQty,
                    'purchase_price' => $r['purchase_price'] ?? 0,
                    'sales_price' => $unitPrice,
                    'total_value' => $stockQty * $unitPrice,
                    'sync_status' => $r['sync_status'] ?? null,
                    'updated_at' => $r['updated_at'] ?? null,
                ];
                
                // Apply local filters
                $passesFilter = true;
                
                if ($categoryFilter && $item['foot_ware_categories_name'] != $categoryFilter) {
                    $passesFilter = false;
                }
                
                if ($typeFilter && $item['type_name'] != $typeFilter) {
                    $passesFilter = false;
                }
                
                if ($stockStatusFilter) {
                    switch ($stockStatusFilter) {
                        case 'in_stock':
                            if ($stockQty <= 0) $passesFilter = false;
                            break;
                        case 'low_stock':
                            if ($stockQty >= 10 || $stockQty <= 0) $passesFilter = false;
                            break;
                        case 'out_of_stock':
                            if ($stockQty > 0) $passesFilter = false;
                            break;
                    }
                }
                
                if ($passesFilter) {
                    $transformed[] = $item;
                    $currentPageData[] = $item;
                    
                    // Calculate current page summary
                    $currentPageSummary['total_quantity'] += $stockQty;
                    $currentPageSummary['total_value'] += ($stockQty * $unitPrice);
                    $currentPageSummary['total_purchased'] += $r['total_purchased_quantity'] ?? 0;
                    $currentPageSummary['total_sold'] += $r['total_sold_quantity'] ?? 0;
                }
            }
            
            $currentPageSummary['total_items'] = count($currentPageData);

            $payload = [
                'draw' => $draw,
                'recordsTotal' => $total,
                'recordsFiltered' => count($transformed),
                'data' => $transformed,
                'all_data_summary' => $allDataSummary, // For top cards (ALL data)
                'current_page_summary' => $currentPageSummary, // For footer (CURRENT page only)
                'pagination' => [
                    'current_page' => $currentPage,
                    'per_page' => $perPage,
                    'total' => $total,
                    'last_page' => $lastPage,
                ]
            ];

            Log::info('==== getStockData END ====', [
                'returned_rows' => count($transformed),
                'all_data_items' => $total,
                'current_page_items' => count($currentPageData),
                'all_data_summary' => $allDataSummary,
                'current_page_summary' => $currentPageSummary
            ]);

            return response()->json($payload, 200);

        } catch (\Throwable $e) {
            Log::error('Stock data fetch error', ['msg' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
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
     * Export stock data as CSV - Updated to match new requirements
     */
    public function exportCsv(Request $request, Store $store)
    {
        $route = StoreRoute::where('store_id', $store->id)
            ->where('endpoint', 'api/manager/data/final-stock-data')
            ->where('is_active', true)
            ->first();

        if (! $route || ! $route->base_url) {
            abort(404, 'Stock data endpoint not configured');
        }

        try {
            $posUrl = rtrim($route->base_url, '/') . '/' . ltrim($route->endpoint, '/');
            $allData = [];
            $page = 1;
            $perPage = 1000;

            do {
                $query = [
                    'page' => $page,
                    'per_page' => $perPage,
                ];

                if ($request->filled('search')) {
                    $query['search'] = $request->input('search');
                }

                $response = $this->tokenService->sendAuthorized(
                    $store,
                    (int) Auth::id(),
                    'GET',
                    $posUrl,
                    [
                        'timeout' => 120,
                        'query' => $query,
                    ]
                );

                if (! $response->successful()) {
                    abort(500, 'POS API request failed');
                }

                $data = $response->json();

                if (! ($data['ok'] ?? false)) {
                    abort(500, 'Failed to fetch data from POS');
                }

                $pageData = $data['data']['data'] ?? [];
                $allData = array_merge($allData, $pageData);

                $currentPage = $data['data']['current_page'] ?? $page;
                $lastPage = $data['data']['last_page'] ?? 1;

                if ($currentPage >= $lastPage) {
                    break;
                }

                $page++;
            } while (true);

            if (empty($allData)) {
                abort(404, 'No stock data found');
            }

            $filteredData = $allData;

            if ($request->filled('category')) {
                $category = $request->input('category');
                $filteredData = array_filter($filteredData, fn ($item) => ($item['foot_ware_categories_name'] ?? '') == $category);
            }

            if ($request->filled('type')) {
                $type = $request->input('type');
                $filteredData = array_filter($filteredData, fn ($item) => ($item['type_name'] ?? '') == $type);
            }

            $filename = 'stock_report_' . $store->name . '_' . date('Y-m-d_H-i-s') . '.csv';

            $headers = [
                'Content-Type' => 'text/csv; charset=utf-8',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            ];

            $callback = function () use ($filteredData) {
                $file = fopen('php://output', 'w');
                fwrite($file, "\xEF\xBB\xBF");

                fputcsv($file, [
                    'Product Name',
                    'Article',
                    'Barcode',
                    'Category',
                    'Type',
                    'Material',
                    'Brand',
                    'Color',
                    'Size',
                    'Purchase Quantity',
                    'Sold Quantity',
                    'Stock',
                    'Unit Price',
                    'Total Value',
                ]);

                foreach ($filteredData as $row) {
                    $stockQty = $row['final_quantity'] ?? 0;
                    $unitPrice = $row['sales_price'] ?? 0;

                    fputcsv($file, [
                        $row['product_material_name'] ?? '',
                        $row['article'] ?? '',
                        $row['barcode'] ?? '',
                        $row['foot_ware_categories_name'] ?? '',
                        $row['type_name'] ?? '',
                        $row['material_type_name'] ?? '',
                        $row['brand_type_name'] ?? '',
                        $row['colors_name'] ?? '',
                        $row['size_name'] ?? '',
                        $row['total_purchased_quantity'] ?? 0,
                        $row['total_sold_quantity'] ?? 0,
                        $stockQty,
                        $unitPrice,
                        $stockQty * $unitPrice,
                    ]);
                }

                fclose($file);
            };

            return response()->stream($callback, 200, $headers);
        } catch (\Throwable $e) {
            Log::error('Stock data export error: ' . $e->getMessage());
            abort(500, 'Export failed: ' . $e->getMessage());
        }
    }
}
