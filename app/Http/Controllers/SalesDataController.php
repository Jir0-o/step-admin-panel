<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Store;
use App\Models\StoreRoute;
use App\Services\StoreTokenService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class SalesDataController extends Controller
{
    public function __construct(private readonly StoreTokenService $tokenService)
    {
    }

    /**
     * Display sales report page
     */
    public function index(Store $store)
    {
        return view('backend.admin.sales-data', compact('store'));
    }

    /**
     * Get sales data via AJAX for DataTable
     */
    public function getSalesData(Request $request, Store $store)
    {
        Log::info('==== getSalesData START ====', [
            'store_id' => $store->id,
            'request' => $request->all(),
        ]);

        $route = StoreRoute::where('store_id', $store->id)
            ->where('endpoint', 'api/manager/data/sales-data')
            ->where('is_active', true)
            ->first();

        if (!$route || !$route->base_url) {
            Log::warning('Sales endpoint not configured', ['store_id' => $store->id]);

            return response()->json([
                'ok' => false,
                'message' => 'Sales data endpoint not configured for this store',
            ], 400);
        }

        try {
            // Build POS URL same style as stock controller
            $baseUrl = rtrim($route->base_url, '/');
            $endpoint = ltrim($route->endpoint, '/');

            if (
                str_contains($baseUrl, '/api/manager/data/sales-data') ||
                str_ends_with($baseUrl, '/sales-data')
            ) {
                $posUrl = $baseUrl;
            } else {
                $posUrl = $baseUrl . '/' . $endpoint;
            }

            Log::info('Calling POS sales API', [
                'store_id' => $store->id,
                'pos_url' => $posUrl,
            ]);

            $start = max(0, (int) $request->input('start', 0));
            $length = max(1, (int) $request->input('length', 100));
            $draw = (int) $request->input('draw', 1);
            $page = (int) floor($start / $length) + 1;

            $query = [
                'page' => $page,
                'per_page' => $length,
            ];

            // Date filters
            if ($request->filled('from_date')) {
                $query['from_date'] = $request->input('from_date');
            }

            if ($request->filled('to_date')) {
                $query['to_date'] = $request->input('to_date');
            }

            // Search
            if ($request->filled('search.value')) {
                $query['search'] = $request->input('search.value');
            } elseif ($request->filled('search_sales')) {
                $query['search'] = $request->input('search_sales');
            }

            // Ordering
            if ($request->has('order.0.column') && $request->has('order.0.dir')) {
                $columnIndex = (int) $request->input('order.0.column');
                $orderDir = $request->input('order.0.dir', 'desc');

                $columnMap = [
                    0  => 'cart_id',
                    1  => 'cart_date',
                    2  => 'customer_mobile',
                    3  => 'payment_method',
                    4  => 'total_amount',
                    5  => 'vat_amount',
                    6  => 'total_discount',
                    7  => 'paid_amount',
                    8  => 'due_amount',
                    9  => 'gross_profit',
                    10 => 'net_profit',
                    11 => 'created_by',
                    12 => 'waiter_name',
                ];

                if (isset($columnMap[$columnIndex])) {
                    $query['order_by'] = $columnMap[$columnIndex];
                    $query['order_dir'] = in_array(strtolower($orderDir), ['asc', 'desc']) ? $orderDir : 'desc';
                }
            }

            Log::info('POS sales query params', [
                'store_id' => $store->id,
                'query' => $query,
            ]);

            $response = $this->tokenService->sendAuthorized(
                $store,
                (int) Auth::id(),
                'GET',
                $posUrl,
                [
                    'timeout' => 90,
                    'query' => array_filter($query),
                ]
            );

            Log::info('POS sales HTTP status', [
                'store_id' => $store->id,
                'status' => $response->status(),
                'body_preview' => mb_substr($response->body(), 0, 1000),
            ]);

            if (!$response->successful()) {
                Log::error('POS sales returned non-success', [
                    'store_id' => $store->id,
                    'status' => $response->status(),
                    'body' => $response->body(),
                    'pos_url' => $posUrl,
                ]);

                return response()->json([
                    'draw' => $draw,
                    'recordsTotal' => 0,
                    'recordsFiltered' => 0,
                    'data' => [],
                    'error' => 'Failed to fetch sales data from POS: ' . $response->status(),
                    'upstream_body' => mb_substr($response->body(), 0, 1000),
                ], 502);
            }

            $body = $response->json();

            if (!isset($body['ok']) || $body['ok'] !== true) {
                Log::warning('POS sales returned ok:false', [
                    'store_id' => $store->id,
                    'body' => $body,
                ]);

                return response()->json([
                    'draw' => $draw,
                    'recordsTotal' => 0,
                    'recordsFiltered' => 0,
                    'data' => [],
                    'error' => $body['message'] ?? 'POS returned error',
                ], 422);
            }

            $pageData = $body['data'] ?? [];
            $rows = $pageData['data'] ?? [];
            $total = (int) ($pageData['total'] ?? 0);
            $perPage = (int) ($pageData['per_page'] ?? $length);
            $currentPage = (int) ($pageData['current_page'] ?? $page);
            $lastPage = (int) ($pageData['last_page'] ?? 1);

            // Summary for all data from POS response
            $allDataSummary = [
                'total_items' => $total,
                'total_amount' => 0,
                'total_vat' => 0,
                'total_discount' => 0,
                'total_payable_amount' => 0,
                'final_total_amount' => 0,
                'total_paid' => 0,
                'total_due' => 0,
                'total_gross_profit' => 0,
                'total_net_profit' => 0,
                'total_customers' => 0,
            ];

            if (isset($body['data']['summary'])) {
                $summaryData = $body['data']['summary'];
                $allDataSummary = [
                    'total_items' => $summaryData['total_items'] ?? $total,
                    'total_amount' => $summaryData['total_amount'] ?? 0,
                    'total_vat' => $summaryData['total_vat'] ?? 0,
                    'total_discount' => $summaryData['total_discount'] ?? 0,
                    'total_payable_amount' => $summaryData['total_payable_amount'] ?? 0,
                    'final_total_amount' => $summaryData['final_total_amount'] ?? 0,
                    'total_paid' => $summaryData['total_paid'] ?? 0,
                    'total_due' => $summaryData['total_due'] ?? 0,
                    'total_gross_profit' => $summaryData['total_gross_profit'] ?? 0,
                    'total_net_profit' => $summaryData['total_net_profit'] ?? 0,
                    'total_customers' => $summaryData['total_customers'] ?? 0,
                ];
            }

            $transformed = [];
            $currentPageSummary = [
                'total_items' => 0,
                'total_amount' => 0,
                'total_vat' => 0,
                'total_discount' => 0,
                'total_payable_amount' => 0,
                'final_total_amount' => 0,
                'total_paid' => 0,
                'total_due' => 0,
                'total_gross_profit' => 0,
                'total_net_profit' => 0,
            ];

            foreach ($rows as $r) {
                $items = is_array($r['items'] ?? null) ? $r['items'] : [];
                $itemsText = '';

                if (!empty($items)) {
                    $itemCount = count($items);
                    $displayItems = array_slice($items, 0, 3);

                    foreach ($displayItems as $item) {
                        $barcode = $item['barcode'] ?? '';
                        $quantity = $item['quantity'] ?? 0;
                        $brand = $item['brand'] ?? '';
                        $itemsText .= e($barcode) . ' (' . e((string) $quantity) . ') ' . e($brand) . '<br>';
                    }

                    if ($itemCount > 3) {
                        $itemsText .= '<small class="text-muted">+' . ($itemCount - 3) . ' more items</small>';
                    }
                }

                $item = [
                    'DT_RowId' => 'sale_' . ($r['cart_id'] ?? uniqid()),
                    'cart_id' => $r['cart_id'] ?? null,
                    'trx_number' => $r['trx_number'] ?? '',
                    'cart_date' => $r['cart_date'] ?? '',
                    'customer_mobile' => $r['customer_mobile'] ?? '',
                    'payment_method' => $r['payment_method'] ?? '',
                    'total_amount' => (float) ($r['total_cart_amount'] ?? 0),
                    'vat_amount' => (float) ($r['vat_amount'] ?? 0),
                    'discount' => (float) ($r['total_discount'] ?? 0),
                    'total_payable_amount' => (float) ($r['total_payable_amount'] ?? 0),
                    'final_total_amount' => (float) ($r['final_total_amount'] ?? 0),
                    'paid_amount' => (float) ($r['paid_amount'] ?? 0),
                    'due_amount' => (float) ($r['due_amount'] ?? 0),
                    'gross_profit' => (float) ($r['gross_profit'] ?? 0),
                    'net_profit' => (float) ($r['net_profit'] ?? 0),
                    'created_by' => $r['created_by'] ?? '',
                    'waiter_name' => $r['waiter_name'] ?? '',
                    'table_no' => $r['table_no'] ?? '',
                    'sales_type' => $r['sales_type'] ?? '',
                    'items_html' => $itemsText,
                    'items_count' => count($items),
                    'items' => $items,
                ];

                $transformed[] = $item;

                $currentPageSummary['total_items']++;
                $currentPageSummary['total_amount'] += $item['total_amount'];
                $currentPageSummary['total_vat'] += $item['vat_amount'];
                $currentPageSummary['total_discount'] += $item['discount'];
                $currentPageSummary['total_payable_amount'] += $item['total_payable_amount'];
                $currentPageSummary['final_total_amount'] += $item['final_total_amount'];
                $currentPageSummary['total_paid'] += $item['paid_amount'];
                $currentPageSummary['total_due'] += $item['due_amount'];
                $currentPageSummary['total_gross_profit'] += $item['gross_profit'];
                $currentPageSummary['total_net_profit'] += $item['net_profit'];
            }

            $payload = [
                'draw' => $draw,
                'recordsTotal' => $total,
                'recordsFiltered' => $total,
                'data' => $transformed,
                'all_data_summary' => $allDataSummary,
                'current_page_summary' => $body['data']['page_summary'] ?? $currentPageSummary,
                'pagination' => [
                    'current_page' => $currentPage,
                    'per_page' => $perPage,
                    'total' => $total,
                    'last_page' => $lastPage,
                ],
            ];

            Log::info('==== getSalesData END ====', [
                'store_id' => $store->id,
                'returned_rows' => count($transformed),
                'all_data_items' => $total,
                'current_page_items' => count($transformed),
                'all_data_summary' => $allDataSummary,
                'current_page_summary' => $payload['current_page_summary'],
            ]);

            return response()->json($payload, 200);

        } catch (\Throwable $e) {
            Log::error('Sales data fetch error', [
                'store_id' => $store->id,
                'msg' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'draw' => $draw ?? 1,
                'recordsTotal' => 0,
                'recordsFiltered' => 0,
                'data' => [],
                'error' => 'Server error: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Export sales data as CSV
     */
    public function exportCsv(Request $request, Store $store)
    {
        $route = StoreRoute::where('store_id', $store->id)
            ->where('endpoint', 'api/manager/data/export-sales-data')
            ->where('is_active', true)
            ->first();

        if (!$route || !$route->base_url) {
            abort(404, 'Sales data export endpoint not configured');
        }

        try {
            $baseUrl = rtrim($route->base_url, '/');
            $endpoint = ltrim($route->endpoint, '/');

            if (
                str_contains($baseUrl, '/api/manager/data/export-sales-data') ||
                str_ends_with($baseUrl, '/export-sales-data')
            ) {
                $posUrl = $baseUrl;
            } else {
                $posUrl = $baseUrl . '/' . $endpoint;
            }

            $query = [];

            if ($request->filled('from_date')) {
                $query['from_date'] = $request->input('from_date');
            }

            if ($request->filled('to_date')) {
                $query['to_date'] = $request->input('to_date');
            }

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

            if ($response->successful()) {
                $filename = 'sales_report_' . $store->name . '_' . date('Y-m-d_H-i-s') . '.csv';

                return response($response->body(), 200, [
                    'Content-Type' => 'text/csv; charset=utf-8',
                    'Content-Disposition' => 'attachment; filename="' . $filename . '"',
                ]);
            }

            abort(500, 'Failed to fetch export data from POS');
        } catch (\Throwable $e) {
            Log::error('Sales data export error: ' . $e->getMessage());
            abort(500, 'Export failed: ' . $e->getMessage());
        }
    }
}